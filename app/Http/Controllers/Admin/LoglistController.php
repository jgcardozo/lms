<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class LoglistController extends Controller
{
	public function __construct()
	{
		$this->middleware('role:Administrator');
	}

	public function index()
	{
		$logs = Activity::paginate(50);

		return view('lms.admin.log')->with('logs', $logs);
	}
}