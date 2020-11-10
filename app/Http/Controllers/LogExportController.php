<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\ESSubjectResource;
use App\Models\Cohort;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogExportController extends Controller
{
    public function __invoke(Request $request)
    {
//        $filters = [
//        "causer"=> "all",    // admin, user or "all"
//            "cohort"=> "all",        // cohort ID or "all"
//            "action"=> "all",        // action ID or "all"
//            "activity"=> "all",  // activity ID or "all"
//            "sort"=> "timestamp",       // po koja kolona se sortira
//            "order"=> "desc",     // asc or desc
//            "fromDate"=> null,   // filter From
//            "toDate"=> null,     // filter To
//            "user_id"=> null    // user ID or null
//        ];

        $filters = $request->get('filters');

        if($filters['user_id']) {
            $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject')->where('user_id',$filters['user_id'])->orderBy('created_at','DESC');
        }
        else {
            $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject')->orderBy('created_at','DESC');

            if ($filters['causer'] === 'admin') {
                $logs = $logs->where('activity_id','=',7);
            } elseif ($filters['causer'] === 'user') {
                $logs = $logs->where('activity_id','!=',7);
            }


            if ($filters['action'] !== 'all') {
                $logs = $logs->where('action_id','=',$filters['action']);
            }

            if ($filters['activity'] !== 'all') {
                $logs = $logs->where('activity_id','=',$filters['activity']);
            }

            if ($filters['fromDate']) {
                $logs = $logs->where('created_at','>=', date("Y-m-d H:i:s", strtotime($filters['fromDate'])));
            }

            if ($filters['toDate']) {
                $logs = $logs->where('created_at','<=', date("Y-m-d H:i:s", strtotime($filters['toDate'])));
            }

            if($filters['cohort'] !== 'all') {
                $cohort = Cohort::find($filters['cohort']);
                $userIds = $cohort->users()->pluck('users.id');
                $logs = $logs->whereIn('user_id', $userIds);
            }

            $logs = $logs->orderBy('created_at', 'DESC');
        }

        $response = new StreamedResponse(function() use ($logs){
            // Open output stream
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                'ID',
                'User',
                'Action',
                'Subject',
                'Timestamp'
            ]);

            // Get all users
            $logs->chunk(5000, function($chunk) use($handle) {
                foreach ($chunk as $log) {
                    $subject = new ESSubjectResource($log);
                    // Add a new row with data
                    fputcsv($handle, [
                        $log->id,
                        $log->user !== null ? $log->user->name : $log->deleted_user,
                        $log->action !== null ? $log->action->name : "",
                        $subject->resolve()['tree'],
                        $log->created_at
                    ]);
                }
            });

            // Close the output stream
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export.csv"',
        ]);

        return $response;
    }
}
