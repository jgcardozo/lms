<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
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

		$newUser = new User();
		$newUser->contact_id = $request->get('contactId');
		$newUser->name = $request->get('email');
		$newUser->email = $request->get('email');
		$newUser->password = bcrypt(str_random(8));
		$newUser->save();

		$profile = new Profile();
		$profile->phone1 = $request->get('phone');
		$profile->company = $request->get('company');
		$profile->address = $request->get('address');
		$newUser->profile()->save($profile);

		$newUser->assignRole('Customer');

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

	public function store(Request $request)
	{
		$rules = [
			'name' => 'required',
			'email' => 'required',
			'phone1' => 'required'
		];

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails())
		{
			return redirect()->back(); // TODO: Return with errors
		}

		$user = Auth::user();
		$user->name = $request->get('name');
		$user->email = $request->get('email');
		$user->save();

		$profile = $user->profile ?: new Profile();
		$profile->phone1 = $request->get('phone1');
		$profile->phone2 = $request->get('phone2');
		$profile->company = $request->get('company');
		$profile->address = $request->get('address');
		$user->profile()->save($profile);

		return redirect()->back();
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

		return redirect()->back();
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
}
