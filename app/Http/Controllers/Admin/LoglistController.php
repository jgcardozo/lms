<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class LoglistController extends Controller
{
	public function index()
	{
		$logs = Activity::get();

		return view('lms.admin.log')->with('logs', $logs);
	}
}
