<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use Auth;
use Autologin;
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
        \Log::error(['user_registered', $request->all()]);
        $rules = [
            'contactId' => 'required|numeric',
            'email' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            activity('user-registered-failed')->withProperties(['contactID' => request()->get('contactId')])->log('New user with Infusionsoft ID <strong>:properties.contactID</strong> failed to register.');
            return;
        }

        if (!User::where('contact_id', $request->get('contactId'))
            ->orWhere('email', request()->get('email'))
            ->get()
            ->isEmpty()) {
            $user = User::where('contact_id', $request->get('contactId'))
                ->orWhere('email', request()->get('email'))
                ->get()
                ->first();
            
            if (!$user->contact_id) {
                $user->contact_id = $request->get('contactId');
                $user->save();
            }

            if($request->has('lmsrole') && $request->filled('lmsrole')) {
                $role = $request->get('lmsrole');

                if($role != 1 || $role != 2) {
                    $user->roles()->sync($role);
                }
            }

            if ($request->filled('cohortId')) {
                $cohortId = $request->input('cohortId');

                if ($user->cohorts->where('id', $cohortId)->count() == 0 && Cohort::where('id', $cohortId)->count() > 0) {
                    $user->cohorts()->attach($cohortId);
                }
            }

            $user->syncIsTags();

            Mail::to($user)->send(new \App\Mail\ExistingUser($user->email));

            return;
        }

        $password = str_random(16);
        $uuid = uniqid();

        $newUser = new User();
        $newUser->contact_id = $request->get('contactId');
        $newUser->name = $request->get('email');
        $newUser->email = $request->get('email');
        $newUser->password = bcrypt($password);
        $newUser->activation_code = $uuid;
        $newUser->save();

        if ($request->filled('cohortId')) {
            $cohortId = $request->input('cohortId');

            $newUser->cohorts()->attach($cohortId);
        }


        $profile = new Profile();
        $profile->first_name = $request->has('firstname') ? $request->get('firstname') : '';
        $profile->last_name = $request->has('lastname') ? $request->get('lastname') : '';
        $profile->phone1 = $request->has('phone') ? $request->get('phone') : '';
        $newUser->profile()->save($profile);

        $newUser->assignRole('Customer');

        Mail::to($newUser)->send(new \App\Mail\UserRegistered($uuid, $newUser->email));
        activity('user-registered-success')->causedBy($newUser)->log('New user with email: <strong>:causer.email</strong> registered.');
    }

    public function activateShow($uuid)
    {
        $user = User::where('activation_code', $uuid)->get()->first();

        if (is_null($user)) {
            return redirect('/');
        }

        return view('auth.passwords.activate')->with('uuid', $uuid);
    }

    public function activateIt($uuid)
    {
        $user = User::where('activation_code', $uuid)->get()->first();

        if (is_null($user)) {
            return redirect('/');
        }

        $rules = [
            'password' => 'required|confirmed'
        ];

        $validator = Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user->password = bcrypt(request()->get('password'));
        $user->activation_code = '';
        $user->save();

        return redirect('/');
    }

    public function profile()
    {
        $user = Auth::user();

        $timezones = \timezoneList();

        return view('lms.user.profile')->with(['user' => $user])->with('timezones', $timezones);
    }

    public function settings()
    {
        return view('lms.user.settings');
    }
    
    public function billing()
    {
        // return view('lms.user.billing');

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
            'cc_name' => $request->get('cc_name'),
            'cc_address' => $request->get('cc_address'),
            'cc_number' => $request->get('cc_number'),
            'cc_expiry_month' => $request->get('cc_expiry_month'),
            'cc_expiry_year' => $request->get('cc_expiry_year'),
            // 'cc_cvv' => $request->has('cc_cvv') ? $request->get('cc_cvv') : ''
            'cc_cvv' => ''
        ];

        // Add new credit card for this invoice Id
        $newCC = InfusionsoftFlow::createCreditCard(Auth::user(), $creditCard);
        if (!$newCC->status) {
            return response()->json([
                'status' => false,
                'message' => $newCC->message
            ]);
        }

        $datetime = new \DateTime('now', new \DateTimeZone('America/New_York'));

        $updateCC = InfusionsoftFlow::is()->invoices()->addPaymentPlan($invoice_id, true, $newCC->id, 2, 1, 3, (double)0, $datetime, $datetime, 7, 1);
        $charge = InfusionsoftFlow::is()->invoices()->chargeInvoice($invoice_id, '', $newCC->id, 2, false);

        addISCreditCard(Auth::user()->id, $request->get('course_id'), $newCC->id);

        if (strtolower($charge['Code']) == 'declined') {
            return response()->json([
                'status' => false,
                'message' => 'Your credit card was declined.'
            ]);
        }

        if (strtolower($charge['Code']) == 'error') {
            return response()->json([
                'status' => false,
                'message' => 'There was an error with this credit card. Try again or contact support.'
            ]);
        }

        mixPanel()->track('Updated billing details');

        $log = new \App\Models\Log;
        $log->user_id = Auth::user()->id;
        $log->action_id = 8;
        $log->activity_id = 3;
        $log->save();

        return response()->json([
            'status' => true,
            'message' => 'Your credit card has been successfully processed.'
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

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $user->name = $request->get('first_name') . ' ' . $request->get('last_name');
        $user->email = $request->get('email');
        $user->timezone = $request->has('timezone') ? $request->get('timezone') : '';
        $user->save();

        $profile = $user->profile ?: new Profile();
        $profile->first_name = $request->has('first_name') ? $request->get('first_name') : '';
        $profile->last_name = $request->has('last_name') ? $request->get('last_name') : '';
        $profile->phone1 = $request->has('phone1') ? $request->get('phone1') : '';
        $profile->company = $request->has('company') ? $request->get('company') : '';
        $user->profile()->save($profile);

        mixPanel()->track('Updated contact details');

        $log = new \App\Models\Log;
        $log->user_id = $user->id;
        $log->action_id = 8;
        $log->activity_id = 5;
        $log->save();

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

        if ($validator->fails()) {
            return redirect()->back(); // TODO: Return with errors
        }

        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();

        mixPanel()->track('Changed password');

        $log = new \App\Models\Log;
        $log->user_id = $user->id;
        $log->action_id = 8;
        $log->activity_id = 4;
        $log->save();



        return redirect()->back()->with('message', 'Password successfully updated');
    }

    public function notifications()
    {
        $notifications = [];
        $notifications['general'] = Auth::user()->notifications ->where('type', '!=', 'App\Notifications\UnlockedByTag');
        $notifications['gamification'] = Auth::user()->notifications ->where('type', 'App\Notifications\Gamification');

        return view('lms.notifications.index')->with('user_notifications', $notifications);
    }

    public function notificationsMarkAsRead()
    {
        Auth::user()->notifications->markAsRead();
    }

    public function autologin(Request $request)
    {
        if (Auth::check()) {
            return redirect('/');
        }

        $id = $request->get('id');
        $mail = $request->get('email');
        $key = $request->get('key');

        if (Autologin::validate($id, $mail, $key)) {
            $user = User::find($id);
            Auth::loginUsingId($user->id);
            return redirect('/set-session');
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
        \Log::error(['user_synced', $request->all()]);

        if (!request()->has('contactId')) {
            return false;
        }

        $user = User::where('contact_id', request()->get('contactId'))
            ->orWhere('email', request()->get('email'))
            ->get()
            ->first();
            
        if (empty($user)) {
            return false;
        }

        if($request->has('lmsrole') && $request->filled('lmsrole')) {
            $role = $request->get('lmsrole');

            if($role != 1 || $role != 2) {
                $user->roles()->sync($role);
            }
        }
        
        if (!$user->contact_id) {
            $user->contact_id = $request->get('contactId');
            $user->save();
        }


        // Sync Infusionsoft user tags
        $user->syncIsTags();
    }
}
