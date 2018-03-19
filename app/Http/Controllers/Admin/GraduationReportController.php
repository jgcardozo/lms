<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class GraduationReportController extends Controller
{
    public function failTest()
    {
        $users = $this->failTestUsers();
        $columns = ['User ID', 'Name', 'Email', 'Contact ID', 'Cohort', 'Score', 'cohort ID'];
        $properties = ['id', 'name', 'email', 'contact_id', 'cohorts_name', 'score', 'cohort_id'];
        $fileName = 'graduation-fail-test.csv';

        return $this->downloadCSV($fileName, $columns, $properties, $users);
    }

    public function finishedCourse()
    {
        $users = $this->finishedCourseUsers();
        $columns = ['User ID', 'Name', 'Email', 'Contact ID', 'Cohort', 'cohort ID'];
        $properties = ['id', 'name', 'email', 'contact_id', 'cohorts_name', 'cohort_id'];
        $fileName = 'graduation-course-finished-not-test.csv';

        return $this->downloadCSV($fileName, $columns, $properties, $users);
    }

    protected function downloadCSV($fileName, $columns, $properties, $rows)
    {
        $fileNamePath = 'downloadables/' . $fileName;

        $file = fopen($fileNamePath, 'w');
        fputcsv($file, $columns);

        foreach ($rows as $row) {
            $data = [];
            foreach ($properties as $property) {
                $data[] = $row->{$property};
            }
            fputcsv($file, $data);
        }
        fclose($file);

        $headers = [
            'Content-type' => 'text/csv'
        ];

        return Response::download($fileNamePath, $fileName, $headers);
    }

    protected function failTestUsers()
    {
        return \DB::select('
            SELECT 
                users.id, users.name, LCASE(users.email) as email, users.contact_id, cohorts.name as cohorts_name, results.score, users.cohort_id
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
                users.id ASC;');
    }

    protected function finishedCourseUsers()
    {
        return \DB::select('
            SELECT 
                users.id, users.name, LCASE(users.email) as email, users.contact_id, cohorts.name as cohorts_name, users.cohort_id
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
            ORDER BY 
                users.id ASC;');
    }

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
