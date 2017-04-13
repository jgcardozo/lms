<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserLoginsController extends Controller
{
	public function __construct()
	{
		$this->middleware('role:Administrator');
	}

	public function index()
	{
		$num = lms_get_setting('max_ip_logins', 10);
		$userLogins = DB::table('user_logins')->select(DB::raw('user_id, COUNT(ip) as count'))->groupBy('user_id')->having('count', '>=', (int)$num)->get();

		return view('lms.admin.userlogins')->with('logins', $userLogins);
	}
}