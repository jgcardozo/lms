<?php

namespace App\Models;

use Auth;
use InfusionsoftFlow;
use App\Traits\ISLock;
use App\Models\Session;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use App\Traits\BackpackUpdateLFT;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\IgnoreCoachingCallsScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Activitylog\Traits\LogsActivity;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Course extends Model
{
	use ISLock;
	use Sluggable;
	use CrudTrait;
	use LogsActivity;
	use BackpackCrudTrait;
	use BackpackUpdateLFT;
	use SluggableScopeHelpers;

	protected $fillable = ['title', 'slug', 'short_description', 'description', 'video_url', 'featured_image', 'logo_image', 'apply_now', 'apply_now_label', 'module_group_title', 'lock_date', 'facebook_group_id'];

	/**
	 * Billing attributes
	 */

	public $billing_invoice_id = null;
	public $billing_ccard = null;
	public $billing_plans = [];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(new OrderScope);
	}

	/**
	 * Method that checks if all
	 * starter videos are seen
	 *
	 * @param null $user
	 * @return bool
	 */
	public function areAllStarterSeen($user = null)
	{
		if(!$user) {
			$user = Auth::user();
		}

		$starter_videos = $this->starter_videos->pluck('id')->toArray();
		$watched_videos = $user->sessionsWatched->pluck('id')->toArray();

		$check = array_intersect($starter_videos, $watched_videos);

		if(count($check) == count($starter_videos))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get all sessions that belongs to this course
	 *
	 * @return array
	 */
	public function getAllSessions()
	{
		$tmp = [];

		foreach($this->modules as $module)
		{
			foreach($module->lessons as $lesson)
			{
				$tmp = array_merge($tmp, $lesson->sessions->pluck('id')->toArray());
			}
		}

		return $tmp;
	}

	/**
	 * Get next session to be resumed
	 *
	 * @param null $user
	 * @return bool
	 */
	public function getNextSession($user = null)
	{
		if(!$user)
		{
			$user = Auth::user();
		}

		if(!$this->areAllStarterSeen())
		{
			return false;
		}

		$courseSessions = $this->getAllSessions();
		$watched_videos = $user->sessionsWatched->pluck('id')->toArray();

		foreach($courseSessions as $session)
		{
			if(in_array($session, $watched_videos)) continue;

			return Session::find($session);
			break;
		}

		return true;
	}

	/**
	 * Check if course is locked
	 *
	 * @return bool
	 */
	public function getIsLockedAttribute()
	{
		return $this->is_tag_locked() && !is_role_admin();
	}

	/**
	 * Get image from S3
	 */
	public function getFeaturedImageUrlAttribute()
	{
		// TODO: Check why this is not working
		// $s3image = \Storage::disk('s3')->url($this->featured_image);

		return !empty($this->featured_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . $this->featured_image : '';
	}

	/**
	 * Get image from S3
	 */
	public function getLogoImageUrlAttribute()
	{
		// TODO: Check why this is not working
		// $s3image = \Storage::disk('s3')->url($this->featured_image);

		return !empty($this->logo_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . $this->logo_image : '';
	}

	/**
	 * Setup all billing details attached
	 * to this course from Infusionsoft
	 */
	public function setup_billing($userCards = [])
	{
		$course_products = $this->is_course_products->pluck('product_id')->toArray();
		if(!$course_products)
			return [];

		// Get user invoices
		$invoices = InfusionsoftFlow::is()->data()->query('Invoice', 1000, 0, ['ContactId' => Auth::user()->contact_id], ['Id'], '', false);

		foreach($invoices as $invoice)
		{
			// Get invoice order items and check their Ids against the one in course table
			$invoiceItems = InfusionsoftFlow::is()->data()->query('OrderItem', 1000, 0, ['OrderId' => $invoice['Id']], ['ProductId'], '', false);
			$invoiceItems = array_pluck($invoiceItems, 'ProductId');
			if(!count(array_intersect($course_products, $invoiceItems)))
				continue;

			// Set the Infusionsoft invoice Id to this course
			$this->billing_invoice_id = (int) $invoice['Id'];

			// Try to find out the credit card used for this payment plan on this invoice
			$payments = InfusionsoftFlow::is()->invoices()->getPayments($invoice['Id']);
			if(!empty($payments))
			{
				$charges = InfusionsoftFlow::is()->data()->query('CCharge', 1000, 0, ['Id' => $payments[0]['ChargeId']], ['CCId', 'PaymentId', 'Amt'], '', false);
				if(!empty($charges))
				{
					$this->billing_ccard = (int) $charges[0]['CCId'];

					if(!empty($userCards))
					{
						$ccardIndex = array_search($this->billing_ccard, array_column($userCards, 'Id'));
						if($ccardIndex !== false)
						{
							$this->billing_ccard = $userCards[$ccardIndex];
						}
					}
				}
			}

			// Get pay plans
			$payplan = InfusionsoftFlow::is()->data()->query('PayPlan', 1000, 0, ['InvoiceId' => $invoice['Id']], ['Id'], '', false);
			if(!empty($payplan))
			{
				$payplan_items = InfusionsoftFlow::is()->data()->query('PayPlanItem', 1000, 0, ['PayPlanId' => $payplan[0]['Id']], ['AmtDue', 'AmtPaid', 'DateDue', 'Id', 'PayPlanId', 'Status'], '', false);
				if(!empty($payplan_items))
				{
					$this->billing_plans = $payplan_items;
				}
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
    public function modules()
    {
		return $this->hasMany('App\Models\Module');
	}

	public function starter_videos()
    {
		return $this->hasMany('App\Models\Session', 'starter_course_id');
	}

	public function getRouteKeyName()
    {
		return 'slug';
	}

	public function coachingcall()
    {
		return $this->hasMany('App\Models\CoachingCall');
	}

	public function events()
    {
		return $this->hasMany('App\Events');
	}

	public function sluggable()
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

	public function is_course_products()
	{
		return $this->hasMany('App\Models\ISCourseProductId', 'course_id', 'id');
	}

	/**
	 * Users that have Infusionsoft access to this course
	 */
	public function subscribers($tags)
	{
		// $users = User::
		return $this->lock_tags;
	}

	/*
	|--------------------------------------------------------------------------
	| Mutators
	|--------------------------------------------------------------------------
	*/
	public function setFeaturedImageAttribute($value)
	{
		$attribute_name = 'featured_image';
		$disk = 's3';
		$destination_path = 'courses/';

		$request = \Request::instance();
		$file = $request->file($attribute_name);
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();

		// Make the image
		$image = \Image::make($file);

		// Store the image on disk
		\Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString());

		// Save the path to the database
		$this->attributes[$attribute_name] = $destination_path . $filename;
	}

	public function setLogoImageAttribute($value)
	{
		$attribute_name = 'logo_image';
		$disk = 's3';
		$destination_path = 'courses/';

		$request = \Request::instance();
		$file = $request->file($attribute_name);
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();

		// Make the image
		$image = \Image::make($file);

		// Store the image on disk
		\Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString());

		// Save the path to the database
		$this->attributes[$attribute_name] = $destination_path . $filename;
	}

	/*
	|--------------------------------------------------------------------------
	| Backpack model callbacks
	|--------------------------------------------------------------------------
	*/
	public function view_modules_button()
	{
		?>
		<a href="<?php echo route('crud.module.index', ['course' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View modules
		</a>
		<?php
	}

	public function view_intros_button()
	{
		if(!$this->starter_videos) return;
		?>
		<a href="<?php echo route('crud.session.index', ['course' => $this->id]); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View intros
		</a>
		<?php
	}

	public function view_in_frontend_button()
	{
		?>
		<a target="_blank" href="<?php echo route('single.course', $this->slug); ?>" class="btn btn-xs btn-default">
			<i class="fa fa-eye"></i>
			View course
		</a>
		<?php
	}
}
