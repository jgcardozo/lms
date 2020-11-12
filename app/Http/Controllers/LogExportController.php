<?php

namespace App\Http\Controllers;

use App\ElasticSearch\Repositories\ElasticSearchLogsRepository;
use App\Http\Resources\Admin\ESSubjectResource;
use App\Models\Cohort;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $repo = new ElasticSearchLogsRepository();

        $request->validate([
            '_token' => 'required'
        ]);

        try {
            $datetime = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "ask_lms_logs_{$datetime}.csv";

            $response = new StreamedResponse(function() use ($repo, $request){
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

                $lastId = Log::orderBy('id', 'desc')->first()->id;
                $count = 0;
                $chunkSize = 5000;

                while(true) {

                    if($request->get('user_id')) {
                        $logs = Log::where('user_id',$request->get('user_id'));
                    }
                    else {
                        $logs = Log::query();
                    }


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

                    $newLog = $logs;
                    $logChunk = $newLog->where('id', '>', $count)->where('id', '<=', $count + $chunkSize);

                    $chunk = $logChunk->pluck('id')->toArray();

                    if(count($chunk)) {
                        $esLogs = $repo->getLogsByIds($chunk);
                        foreach ($esLogs['docs'] as $log) {
                            if(!$log['found']) { continue; }

                            $source = $log['_source'];

                            // Add a new row with data
                            fputcsv($handle, [
                                (string)$source['id'],
                                $source['user']['name'] !== null ? $source['user']['name'] : $source['user']['email'],
                                $source['action'] ? $source['action']['name'] : "",
                                $source['subject']['tree'],
                                $source['created_at']
                            ]);
                        }
                    }

                    $count += $chunkSize;

                    if($count >= $lastId) {
                        break;
                    }
                }

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
}
