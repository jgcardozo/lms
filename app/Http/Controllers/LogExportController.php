<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\ESSubjectResource;
use App\Models\Cohort;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            '_token' => 'required'
        ]);

        if($request->get('user_id')) {
            $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject')->where('user_id',$request->get('user_id'));
        }
        else {
            $logs = \App\Models\Log::with('user.cohorts')->with('action')->with('activity')->with('subject');

            if ($request->get('causer') === 'admin') {
                $logs = $logs->where('activity_id','=',7);
            } elseif ($request->get('causer') === 'user') {
                $logs = $logs->where('activity_id','!=',7);
            }

            if ($request->get('action') !== 'all') {
                $logs = $logs->where('action_id','=',$request->get('action'));
            }

            if ($request->get('activity') !== 'all') {
                $logs = $logs->where('activity_id','=',$request->get('activity'));
            }

            if ($request->get('fromDate')) {
                $logs = $logs->where('created_at','>=', date("Y-m-d H:i:s", strtotime($request->get('fromDate'))));
            }

            if ($request->get('toDate')) {
                $logs = $logs->where('created_at','<=', date("Y-m-d H:i:s", strtotime($request->get('toDate'))));
            }

            if($request->get('cohort') !== 'all') {
                $cohort = Cohort::find($request->get('cohort'));
                $userIds = $cohort->users()->pluck('users.id');
                $logs = $logs->whereIn('user_id', $userIds);
            }
        }

        $logs = $logs->orderBy(
            $this->getSortColumn($request),
            $request->get('order')
        );


        try {
            $datetime = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "ask_lms_logs_{$datetime}.csv";
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
                'Content-Disposition' => 'attachment; filename="'. $filename .'"',
            ]);

            return $response;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("ERROR IN CSV EXPORT: Code: {$e->getCode()}; Message: {$e->getMessage()}");
            throw $e;
        }
    }

    private function getSortColumn(Request $request)
    {
        switch ($request->get('sort')) {
            case "id":
                return "id";
            case "user":
                return "user.email";
            case "action":
                return "action.name";
            default:
                return "created_at";
        }
    }
}
