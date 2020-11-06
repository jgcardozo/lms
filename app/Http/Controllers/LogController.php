<?php

namespace App\Http\Controllers;

use App\ElasticSearch\Repositories\ElasticSearchLogsRepository;
use App\Models\Cohort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    protected $repo;

    public function __construct()
    {
        $this->repo = new ElasticSearchLogsRepository();
    }

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
        $userFlag = $request->has('user_id');

        $cohorts = \App\Models\Cohort::all();
        $actions = \App\Models\Action::all();
        $activities = \App\Models\Activity::all()->except(7);

        $request->flash();

        return view('lms.admin.logs.index',compact('logs','cohorts','actions','activities','userFlag'));
    }


    public function search(Request $request)
    {
        return $this->repo->search($request);
    }
}
