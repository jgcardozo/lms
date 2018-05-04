<?php

namespace App\Models;

use Auth;
use Illuminate\Support\Facades\DB;
use InfusionsoftFlow;
use App\Traits\ISLock;
use App\Models\Session;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use App\Traits\BackpackUpdateLFT;
use App\Traits\UsearableTimezone;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Activitylog\Traits\LogsActivity;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Course extends Model
{
	use ISLock;
	use Sluggable;
	use CrudTrait;
	use LogsActivity;
	use UsearableTimezone;
	use BackpackCrudTrait;
	use BackpackUpdateLFT;
	use SluggableScopeHelpers;

	protected $fillable = [
		'title', 'slug', 'short_description', 'description', 'video_url', 'video_type_id', 'featured_image', 'logo_image', 'apply_now', 'apply_now_label', 'module_group_title', 'lock_date', 'user_lock_date', 'facebook_group_id', 'payf_tag', 'cancel_tag', 'billing_is_products', 'must_watch', 'complete_feature'
	];

	protected $dates = [
		'user_lock_date'
	];

	protected $casts = [
        'must_watch' => 'must_watch',
        'complete_feature' => 'boolean'
    ];

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

		static::created(function($course) {
            $schedule = new \App\Models\Schedule;
            $schedule->name = "Default for $course->title";
            $schedule->course_id = $course->id;
            $schedule->status = "default";
            $schedule->schedule_type = "dripped";
            $schedule->save();

        });
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

	public function getCourseCanceledAttribute()
	{
		if(Auth::user()->hasTag($this->cancel_tag))
			return true;

		return false;
	}

    /**
     * Check if all the modules within this module
     * have been marked as watched by the user
     *
     * @return bool
     */
    public function getIsCompletedAttribute()
    {
        foreach($this->modules as $module)
        {
            if(!$module->is_completed)
            {
                return false;
            }
        }

        return true;
    }

	/**
	 * Get image from S3
	 */
	public function getFeaturedImageUrlAttribute()
	{
		// TODO: Check why this is not working
		// $s3image = \Storage::disk('s3')->url($this->featured_image);

		return !empty($this->featured_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . rawurlencode($this->featured_image) : '';
	}

	/**
	 * Get image from S3
	 */
	public function getLogoImageUrlAttribute()
	{
		// TODO: Check why this is not working
		// $s3image = \Storage::disk('s3')->url($this->featured_image);

		return !empty($this->logo_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . rawurlencode($this->logo_image) : '';
	}

	public function getCreditCardAttribute()
	{
		$user = Auth::user();

		$cc_id = \DB::table('payment_card_user')
				->select('cc_id')
				->where('user_id', $user->id)
				->where('course_id', $this->id)
				->first();

		if(!empty($cc_id))
		{
			return $cc_id->cc_id;
		}

		return null;
	}

	public function getIsCourseProductsAttribute()
	{
		$items = collect(explode(',', $this->billing_is_products))->map(function($item, $key) {
			return ['product_id' => (int) $item];
		});

		return $items;
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
			$_cc_id = $this->credit_card;
			if(!$_cc_id)
			{
				$payments = InfusionsoftFlow::is()->invoices()->getPayments($invoice['Id']);
				if(!empty($payments))
				{
					$charges = InfusionsoftFlow::is()->data()->query('CCharge', 1000, 0, ['Id' => $payments[0]['ChargeId']], ['CCId', 'PaymentId', 'Amt'], '', false);
					if(!empty($charges))
					{
						$_cc_id = (int) $charges[0]['CCId'];
					}
				}
			}

			if(!empty($userCards))
			{
				$ccardIndex = array_search($_cc_id, array_column($userCards, 'Id'));
				if($ccardIndex !== false)
				{
					$this->billing_ccard = $userCards[$ccardIndex];
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
	public function tags() {
        return $this->morphToMany('App\Models\ISTag', 'lockable', 'is_lockables', 'lockable_id', 'tag_id');
    }

	public function cohorts()
    {
        return $this->hasMany(Cohort::class);
    }

    public function modules()
    {
		return $this->hasMany('App\Models\Module');
	}

	public function starter_videos()
    {
		return $this->hasMany('App\Models\Session', 'starter_course_id');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
	public function coachingcall()
    {
		return $this->hasMany('App\Models\CoachingCall')->where('featured_training_coachingcall', false)->orWhere('featured_training_coachingcall', null);
	}

	public function featured_coachingcall()
	{
		return $this->hasOne('App\Models\CoachingCall')->withoutGlobalScopes()->where('featured_training_coachingcall', true);
	}

	public function featured_training()
	{
		return $this->hasOne('App\Models\Training')->withoutGlobalScopes()->where('featured_training_coachingcall', true);
	}

    public function video_type()
    {
        return $this->belongsTo('App\Models\VideoType');
    }

	public function events()
	{
		return $this->hasMany('App\Models\Event');
	}

	public function sluggable()
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

	public function getRouteKeyName()
	{
		return 'slug';
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
