<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use InfusionsoftFlow;
use App\Models\Course;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\UnlockedByTag;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function __construct(User $user)
	{
		//
	}

	/**
	 *
	 * @param Request $request
	 */
	public function register(Request $request)
	{
		$rules = [
			'contactId' => 'required|numeric',
			'email' => 'required'
		];

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails())
		{
			activity('user-registered-failed')->withProperties(['contactID' => request()->get('contactId')])->log('New user with Infusionsoft ID <strong>:properties.contactID</strong> failed to register.');
			return;
		}

		if(!User::where('contact_id', $request->get('contactId'))->get()->isEmpty())
		{
			return;
		}

		$password = str_random(8);

		$newUser = new User();
		$newUser->contact_id = $request->get('contactId');
		$newUser->name = $request->get('email');
		$newUser->email = $request->get('email');
		$newUser->password = bcrypt($password);
		$newUser->save();

		$profile = new Profile();
		$profile->first_name = $request->has('firstname') ? $request->get('firstname') : '';
		$profile->last_name = $request->has('lastname') ? $request->get('lastname') : '';
		$profile->phone1 = $request->has('phone') ? $request->get('phone') : '';
		$newUser->profile()->save($profile);

		$newUser->assignRole('Customer');

		Mail::to($newUser)->send(new \App\Mail\UserRegistered($password, $newUser->email));
		activity('user-registered-success')->causedBy($newUser)->log('New user with email: <strong>:causer.email</strong> registered.');
	}

	public function profile()
	{
		$user = Auth::user();

		return view('lms.user.profile')->with(['user' => $user]);
	}

	public function settings()
	{
		return view('lms.user.settings');
	}
	
	public function billing()
	{
		return view('lms.user.billing');

		$userCards = InfusionsoftFlow::getCreditCards(Auth::user()->contact_id);
		$courses = Course::get();

		// Setup billing details for every course. Needs to refactored
		$courses->each->setup_billing($userCards);

		$viewArgs = [
			'courses' => $courses,
			'cards' => $userCards
		];

		return view('lms.user.billing', $viewArgs);
	}

	public function changeCreditCard(Request $request, $invoice_id)
	{
		$creditCard = (object) [
			'cc_number' => $request->get('cc_number'),
			'cc_expiry_month' => $request->get('cc_expiry_month'),
			'cc_expiry_year' => $request->get('cc_expiry_year'),
			// 'cc_cvv' => $request->has('cc_cvv') ? $request->get('cc_cvv') : ''
			'cc_cvv' => ''
		];

		// Add new credit card for this invoice Id
		$newCC = InfusionsoftFlow::createCreditCard(Auth::user(), $creditCard);
		if(!$newCC->status)
		{
			return response()->json([
				'status' => false,
				'message' => $newCC->message
			]);
		}

		$datetime = new \DateTime('now', new \DateTimeZone('America/New_York'));
		$updateCC = InfusionsoftFlow::is()->invoices()->addPaymentPlan($invoice_id, true, $newCC->id, 6, 1, 3, (double)0, $datetime, $datetime, 7, 1);

		return response()->json([
			'status' => true
		]);
	}

	public function store(Request $request)
	{
		$rules = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required',
			'phone1' => 'required'
		];

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$user = Auth::user();
		$user->name = $request->get('first_name') . ' ' . $request->get('last_name');
		$user->email = $request->get('email');
		$user->save();

		$profile = $user->profile ?: new Profile();
        $profile->first_name = $request->has('first_name') ? $request->get('first_name') : '';
        $profile->last_name = $request->has('last_name') ? $request->get('last_name') : '';
        $profile->phone1 = $request->has('phone1') ? $request->get('phone1') : '';
		$profile->company = $request->has('company') ? $request->get('company') : '';
		$user->profile()->save($profile);

		InfusionsoftFlow::syncContactDetails($user);

		return redirect()->back()->with('message', 'Profile successfully updated');
	}

	public function settingsStore(Request $request)
	{
		$rules = [
			'oldpassword' => 'oldpassword',
			'password' => 'required',
			'password_confirmation' => 'required|same:password'
		];

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
			return redirect()->back(); // TODO: Return with errors
		}

		$user = Auth::user();
		$user->password = bcrypt($request->get('password'));
		$user->save();

		return redirect()->back()->with('message', 'Password successfully updated');
	}

	public function notifications()
	{
		$notifications = [];
		$notifications['general'] = Auth::user()->notifications ->where('type', '!=', 'App\Notifications\UnlockedByTag');
		$notifications['gamification'] = Auth::user()->notifications ->where('type', 'App\Notifications\Gamification');

		return view('lms.notifications.index')->with('user_notifications', $notifications);
	}

	public function autologin(Request $request)
	{
		if(Auth::check())
		{
			return redirect('/');
		}

		$id = $request->get('id');
		$mail = $request->get('email');
		$key = $request->get('key');

		if($key != 'f0mmy4Qrcux')
		{
			return redirect('/');
		}

		$user = User::find($id);
		if(!empty($user) && $user->email === $mail)
		{
			Auth::loginUsingId($user->id);
		}

		return redirect('/');
	}
	
	public function viewAlert($key)
	{
		$today = Carbon::today();
		$today->hour = 23;
		$today->minute = 59;
		$today->second = 59;

		session([$key => $today]);
		session()->save();
	}

	public function syncUserTags(Request $request)
	{
		if(!request()->has('contact_id'))
		{
			return false;
		}

		$user = User::where('contact_id', request()->get('contact_id'))->get()->first();
		if(empty($user))
		{
			return false;
		}

		// Sync Infusionsoft user tags
		$is = new InfusionsoftController($user);
		$newTags = $is->sync();

		// Log the new tags
		if(!empty($newTags))
		{
			Log::info('User tags updated. | ID: ' . implode(', ', $newTags));
		}

		// Check for unlocked course/module/lesson/session
		// and notify the user
		$items = $is->checkUnlockedCourses($newTags);
		if(!empty($is))
		{
			foreach($items as $item)
			{
				$user->notify(new UnlockedByTag($item));
			}
		}
	}
}
