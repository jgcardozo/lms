<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cohort;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:Administrator');
    }

    public function index(Request $request)
    {
        $cohorts = Cohort::get();

        $results = $this->filterQuizCompletion($request);

        return view('lms.admin.analytics')
            ->with('cohorts', $cohorts)
            ->with('results', $results);
    }

    public function filterQuizCompletion(Request $request)
    {
        $cohort = $request->get('cohort');

        $retData = [];

        // Set init users query
        $users = User::select('id');

        // Filter users by cohort if there is filter selected
        if ( ! empty($cohort))
        {
            $users->where('cohort_id', '=', $cohort);
        }

        $userIds = $users->get()
                         ->pluck('id')
                         ->toArray();

        $retData['total_users'] = count($userIds);

        $retData['data'] = \DB::table('class_marker_results')
                  ->select(
                      [
                          'class_marker_results.user_id',
                          'users.id',
                          'users.email',
                          'profiles.user_id',
                          'profiles.first_name',
                          'profiles.last_name'
                      ]
                  )
                  ->leftJoin('users', 'users.id', '=', 'class_marker_results.user_id')
                  ->leftJoin('profiles', 'profiles.user_id', '=', 'class_marker_results.user_id')
                  ->whereIn('class_marker_results.user_id', $userIds)
                  ->where('passed', 1)
                  ->get();

        return $retData;
    }
}