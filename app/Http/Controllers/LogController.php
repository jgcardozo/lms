<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
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

    public function index(Request $request)
    {
        $userFlag = false;
        if ($request->has(['_token','causer','cohort','action','activity'])) {

            $request->validate([
                '_token' => 'required'
            ]);

            if($request->has('user_id')) {
                $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject')->where('user_id',$request->input('user_id'))->orderBy('created_at','DESC');
                $userFlag = true;
            } else {
                $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject')->orderBy('created_at','DESC');
            }


            if ($request->input('causer') === 'admin') {
                $logs = $logs->where('activity_id','=',7);
            } elseif ($request->input('causer') === 'user') {
                $logs = $logs->where('activity_id','!=',7);
            }


            if ($request->input('action') !== 'all') {
                $logs = $logs->where('action_id','=',$request->input('action'));
            }

            if ($request->input('activity') !== 'all') {
                $logs = $logs->where('activity_id','=',$request->input('activity'));
            }

            if ($request->filled('fromDate')) {
                $logs = $logs->where('created_at','>=', date("Y-m-d H:i:s", strtotime($request->input('fromDate'))));
            }

            if ($request->filled('toDate')) {
                $logs = $logs->where('created_at','<=', date("Y-m-d H:i:s", strtotime($request->input('toDate'))));
            }

            if($request->input('cohort') !== 'all') {
                $cohort = Cohort::find($request->input('cohort'));
                $userIds = $cohort->users()->pluck('users.id');
                $logs = $logs->whereIn('user_id', $userIds);

                $logs = $logs->orderBy('created_at', 'DESC')->paginate(20000);
            }
        }
        else {
            if($request->has('user_id')) {
                $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject')->where('user_id',$request->input('user_id'))->orderBy('created_at','DESC')->paginate(20000);
                $userFlag = true;
            } else {
                $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject')->orderBy('created_at','DESC')->paginate(20000);
            }
        }

        $cohorts = \App\Models\Cohort::all();
        $actions = \App\Models\Action::all();
        $activities = \App\Models\Activity::all()->except(7);

        $request->flash();

        return view('lms.admin.logs.index',compact('logs','cohorts','actions','activities','userFlag'));
    }
}
