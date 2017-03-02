<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(User $user) {
		//
	}

	public function profile() {
		$user = Auth::user();

		return view('lms.user.profile')->with(['user' => $user]);
	}

	public function settings() {
		return view('lms.user.settings');
	}

	public function store(Request $request) {
		$rules = [
			'name' => 'required',
			'email' => 'required',
			'phone1' => 'required',
			'phone2' => 'required'
		];

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
			return redirect()->back(); // TODO: Return with errors
		}

		$user = Auth::user();
		$user->name = $request->get('name');
		$user->email = $request->get('email');
		$user->save();

		$profile = $user->profile ?: new Profile();
		$profile->phone1 = $request->get('phone1');
		$profile->phone2 = $request->get('phone2');
		$user->profile()->save($profile);

		return redirect()->back();
	}

	public function settingsStore(Request $request) {
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
}
