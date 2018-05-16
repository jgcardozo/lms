<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    public function ajaxLog(Request $request)
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            $action_id = $request->action_id;
            $activity_id = $request->activity_id;
            $log = new \App\Models\Log;
            $log->user_id = $user_id;
            $log->action_id = $action_id;
            $log->activity_id = $activity_id;
            $log->save();

            return response('Successful',200);
        } else {
            return response('Error',401);
        }
    }

    public function index()
    {
        $logs = \App\Models\Log::with('user')->with('action')->with('activity')->with('subject')->orderBy('created_at','DESC')->paginate(20);

        return view('lms.admin.logs.index',compact('logs',$logs));
    }
}
