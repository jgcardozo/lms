<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class GraduationReportController extends Controller
{
    public function index()
    {
    }

    public function failTest()
    {
        $users = collect($this->failTestUsers());
        
        return \Excel::create('Laravel Excel', function ($excel) use ($users) {
            $excel->sheet('Excel sheet', function ($sheet) use ($users) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($users->toArray());
            });
        })->export('xls');
    }

    public function finishedCourse()
    {
        $users = $this->finishedCourseUsers();
        return $users;
    }

    protected function failTestUsers()
    {
        return \DB::select('
            SELECT 
                users.id, users.name, LCASE(users.email) as email, users.contact_id, cohorts.name, results.score, users.cohort_id
            FROM 
                users 
            LEFT JOIN
                cohorts ON cohorts.id = users.cohort_id
            INNER JOIN 
                class_marker_results AS results  ON users.id = results.user_id
            INNER JOIN
                role_users on users.id = role_users.user_id
            WHERE 
                results.passed = 0 AND
                role_users.role_id = 3
            ORDER BY 
                id ASC;');
        /*
        return \DB::table('users')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'users.cohort_id')
            ->join('class_marker_results as results', 'users.id', '=', 'results.user_id')
            ->join('role_users', 'users.id', '=', 'role_users.user_id')
            ->select('users.id', 'users.name', 'users.email as email', 'users.contact_id', 'cohorts.name', 'results.score', 'users.cohort_id')
            ->where('results.passed', '=', '0')
            ->where('role_users.role_id', '=', '3')
            ->orderBy('id', 'ASC')
            ->get();
        */
    }

    protected function finishedCourseUsers()
    {
        return \DB::select('
            SELECT 
                users.id, users.name, LCASE(users.email) as email, users.contact_id, cohorts.name, users.cohort_id
            FROM 
                users
            LEFT JOIN
                cohorts ON cohorts.id = users.cohort_id 
            LEFT JOIN
                class_marker_results AS results ON users.id = results.user_id
            INNER JOIN
                role_users ON users.id = role_users.user_id
            LEFT JOIN 
                activity_log AS act ON act.causer_id = users.id
            WHERE 
                act.log_name = "session-watched" AND 
                act.subject_type = "App\\\Models\\\Session" AND 
                act.subject_id = 97 AND
                role_users.role_id = 3 AND
                results.user_id IS NULL
            GROUP BY
                users.email
            ORDER BY 
                id ASC;');
    }
}
