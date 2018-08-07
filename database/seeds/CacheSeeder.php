<?php

use Illuminate\Database\Seeder;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ISTag;

class CacheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cache::forever('updating','disabled');


        $courses = Course::with('cohorts', 'tags', 'modules.lmsLessons.sessions')->get();


        foreach ($courses as $course) {
            $this->dataForModules($course);

            foreach ($course->modules as $module) {
                $this->dataForLessons($course,$module);

                foreach ($module->lmsLessons as $lesson) {
                    $this->dataForSessions($course,$module,$lesson);
                }
            }

        }

        foreach ($courses as $course) {
            foreach ($course->cohorts as $cohort) {

                $users = $cohort->users()->get()->pluck('id')->toArray();

                $this->dataForModules($course,$users,$cohort);

                foreach ($course->modules as $module) {
                    $this->dataForLessons($course,$module,$users,$cohort);

                    foreach ($module->lmsLessons as $lesson) {
                        $this->dataForSessions($course,$module,$lesson,$users,$cohort);
                    }
                }
            }
        }


        Cache::forever('last_sync', now());
        Cache::forever('updating','');
    }

    private function dataForModules($course,$user = null,$cohort = null)
    {
        $dataForCache = [];

        $colorPallete = [
            '#2a2a72',
            '#3b5249',
            '#34252f',
            '#1c3738',
            '#b68f40',
            '#531253',
            '#5e747f',
            '#aaae8e',
            '#92140c',
            '#111d4a',
            '#f28f3b',
            '#8f7e4f',
            '#191923',
            '#df2935'
        ];

        shuffle($colorPallete);


        $moduleCount = [];
        $lessonCount = [];
        $sessionCount = [];
        $moduleAvgCompletion = [];
        $lessonAvgCompletion = [];

        $modulePieChart = [];
        $lessonPieChart = [];
        $sessionPieChart = [];

        if($course->tags->count() == 0) {
            return true;
        }

        $tag_id = $course->tags->first()->id;

        if($user) {
            $users = $user;
        } else {
            $users = ISTag::with('users')->find($tag_id)->users()->get()->pluck('id')->toArray();
        }


        $totalUsers = count($users);

        if($totalUsers == 0) {
            return true;
        }

        foreach ($course->modules as $module) {
            $moduleCount[$module->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Module' AND progress_id=? AND user_id IN (" . implode(",", $users) . ")", [$module->id]);
            $moduleCount[$module->title] = $moduleCount[$module->title][0]->counted;
            $moduleCount[$module->title] = $this->percentage($totalUsers, $moduleCount[$module->title]);

            if ($module->lmsLessons->count() > 0 && $module->lmsLessons->first()->sessions->count() > 0) {
                $sessionId = $module->lmsLessons->first()->sessions->first()->id;

                $query = DB::select("SELECT CEIL(AVG(t3.diff)) as avg FROM (SELECT t1.user_id,TIMESTAMPDIFF(DAY,t1.st,t2.en) as diff FROM (SELECT created_at  as st,user_id FROM `progresses` WHERE (progress_type LIKE '%Session' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t1
INNER JOIN (SELECT created_at  as en,user_id FROM `progresses` WHERE (progress_type LIKE '%Module' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t2 ON t1.user_id = t2.user_id) t3", [$sessionId, $module->id]);

                $moduleAvgCompletion[$module->title] = $query[0]->avg;

                if ($moduleAvgCompletion[$module->title] == null) {
                    $moduleAvgCompletion[$module->title] = 0;
                }
            } else {
                $moduleAvgCompletion[$module->title] = 0;
            }
        }

        foreach ($moduleCount as $key => $value) {
            $modulePieChart[$key] = $this->percentage(array_sum($moduleCount), $value);
        }
        foreach ($lessonCount as $key => $value) {
            $lessonPieChart[$key] = $this->percentage(array_sum($lessonCount), $value);
        }
        foreach ($sessionCount as $key => $value) {
            $sessionPieChart[$key] = $this->percentage(array_sum($sessionCount), $value);
        }


        if($cohort) {
            $dataForCache[$course->id."-c".$cohort->id] = [
                $modulePieChart,
                $lessonPieChart,
                $sessionPieChart,
                $moduleAvgCompletion,
                $lessonAvgCompletion,
                $colorPallete,
                $moduleCount,
                $lessonCount,
                $sessionCount,
            ];
        } else {
            $dataForCache[$course->id] = [
                $modulePieChart,
                $lessonPieChart,
                $sessionPieChart,
                $moduleAvgCompletion,
                $lessonAvgCompletion,
                $colorPallete,
                $moduleCount,
                $lessonCount,
                $sessionCount,
            ];
        }


        foreach ($dataForCache as $key => $value) {
            Cache::forever($key, $value);
        }
    }

    private function dataForLessons($course, $module_id,$user = null, $cohort = null)
    {
        $dataForCache = [];

        $colorPallete = [
            '#2a2a72',
            '#3b5249',
            '#34252f',
            '#1c3738',
            '#b68f40',
            '#531253',
            '#5e747f',
            '#aaae8e',
            '#92140c',
            '#111d4a',
            '#f28f3b',
            '#8f7e4f',
            '#191923',
            '#df2935'
        ];

        shuffle($colorPallete);


        $moduleCount = [];
        $lessonCount = [];
        $sessionCount = [];
        $moduleAvgCompletion = [];
        $lessonAvgCompletion = [];

        $modulePieChart = [];
        $lessonPieChart = [];
        $sessionPieChart = [];

        if($course->tags->count() == 0) {
            return true;
        }

        $tag_id = $course->tags->first()->id;

        if($user) {
            $users = $user;
        } else {
            $users = ISTag::with('users')->find($tag_id)->users()->get()->pluck('id')->toArray();
        }

        $totalUsers = count($users);

        if($totalUsers == 0) {
            return true;
        }

        foreach ($course->modules as $module) {
            $moduleCount[$module->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Module' AND progress_id=? AND user_id IN (" . implode(",", $users) . ")", [$module->id]);
            $moduleCount[$module->title] = $moduleCount[$module->title][0]->counted;
            $moduleCount[$module->title] = $this->percentage($totalUsers, $moduleCount[$module->title]);

            if ($module->lmsLessons->count() > 0 && $module->lmsLessons->first()->sessions->count() > 0) {
                $sessionId = $module->lmsLessons->first()->sessions->first()->id;

                $query = DB::select("SELECT CEIL(AVG(t3.diff)) as avg FROM (SELECT t1.user_id,TIMESTAMPDIFF(DAY,t1.st,t2.en) as diff FROM (SELECT created_at  as st,user_id FROM `progresses` WHERE (progress_type LIKE '%Session' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t1
INNER JOIN (SELECT created_at  as en,user_id FROM `progresses` WHERE (progress_type LIKE '%Module' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t2 ON t1.user_id = t2.user_id) t3", [$sessionId, $module->id]);

                $moduleAvgCompletion[$module->title] = $query[0]->avg;

                if ($moduleAvgCompletion[$module->title] == null) {
                    $moduleAvgCompletion[$module->title] = 0;
                }
            } else {
                $moduleAvgCompletion[$module->title] = 0;
            }

        }

        foreach ($module_id->lmsLessons as $lesson) {

            $lessonCount[$lesson->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Lesson' AND progress_id=? AND user_id IN (" . implode(",", $users) . ")", [$lesson->id]);
            $lessonCount[$lesson->title] = $lessonCount[$lesson->title][0]->counted;
            $lessonCount[$lesson->title] = $this->percentage($totalUsers, $lessonCount[$lesson->title]);

            $sessionId = $lesson->sessions->first()->id;

            $query = DB::select("SELECT CEIL(AVG(t3.diff)) as avg FROM (SELECT t1.user_id,TIMESTAMPDIFF(DAY,t1.st,t2.en) as diff FROM (SELECT created_at  as st,user_id FROM `progresses` WHERE (progress_type LIKE '%Session' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t1
INNER JOIN (SELECT created_at  as en,user_id FROM `progresses` WHERE (progress_type LIKE '%Lesson' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t2 ON t1.user_id = t2.user_id) t3", [$sessionId, $lesson->id]);

            $lessonAvgCompletion[$lesson->title] = $query[0]->avg;

            if ($lessonAvgCompletion[$lesson->title] == null) {
                $lessonAvgCompletion[$lesson->title] = 0;
            }

        }

        foreach ($moduleCount as $key => $value) {
            $modulePieChart[$key] = $this->percentage(array_sum($moduleCount), $value);
        }
        foreach ($lessonCount as $key => $value) {
            $lessonPieChart[$key] = $this->percentage(array_sum($lessonCount), $value);
        }
        foreach ($sessionCount as $key => $value) {
            $sessionPieChart[$key] = $this->percentage(array_sum($sessionCount), $value);
        }

        if($cohort) {
            $dataForCache[$course->id."-".$module_id->id."-c".$cohort->id] = [
                $modulePieChart,
                $lessonPieChart,
                $sessionPieChart,
                $moduleAvgCompletion,
                $lessonAvgCompletion,
                $colorPallete,
                $moduleCount,
                $lessonCount,
                $sessionCount,
            ];
        } else {
            $dataForCache[$course->id."-".$module_id->id] = [
                $modulePieChart,
                $lessonPieChart,
                $sessionPieChart,
                $moduleAvgCompletion,
                $lessonAvgCompletion,
                $colorPallete,
                $moduleCount,
                $lessonCount,
                $sessionCount,
            ];
        }


        foreach ($dataForCache as $key => $value) {
            Cache::forever($key, $value);
        }
    }

    private function dataForSessions($course,$moduleM,$lessonL,$user = null,$cohort = null)
    {
        $dataForCache = [];

        $colorPallete = [
            '#2a2a72',
            '#3b5249',
            '#34252f',
            '#1c3738',
            '#b68f40',
            '#531253',
            '#5e747f',
            '#aaae8e',
            '#92140c',
            '#111d4a',
            '#f28f3b',
            '#8f7e4f',
            '#191923',
            '#df2935'
        ];

        shuffle($colorPallete);


        $moduleCount = [];
        $lessonCount = [];
        $sessionCount = [];
        $moduleAvgCompletion = [];
        $lessonAvgCompletion = [];

        $modulePieChart = [];
        $lessonPieChart = [];
        $sessionPieChart = [];

        if($course->tags->count() == 0) {
            return true;
        }
        $tag_id = $course->tags->first()->id;

        if($user) {
            $users = $user;
        } else {
            $users = ISTag::with('users')->find($tag_id)->users()->get()->pluck('id')->toArray();
        }

        $totalUsers = count($users);

        if($totalUsers == 0) {
            return true;
        }

        foreach ($course->modules as $module) {
            $moduleCount[$module->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Module' AND progress_id=? AND user_id IN (" . implode(",", $users) . ")", [$module->id]);
            $moduleCount[$module->title] = $moduleCount[$module->title][0]->counted;
            $moduleCount[$module->title] = $this->percentage($totalUsers, $moduleCount[$module->title]);

            if ($module->lmsLessons->count() > 0 && $module->lmsLessons->first()->sessions->count() > 0) {
                $sessionId = $module->lmsLessons->first()->sessions->first()->id;

                $query = DB::select("SELECT CEIL(AVG(t3.diff)) as avg FROM (SELECT t1.user_id,TIMESTAMPDIFF(DAY,t1.st,t2.en) as diff FROM (SELECT created_at  as st,user_id FROM `progresses` WHERE (progress_type LIKE '%Session' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t1
INNER JOIN (SELECT created_at  as en,user_id FROM `progresses` WHERE (progress_type LIKE '%Module' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t2 ON t1.user_id = t2.user_id) t3", [$sessionId, $module->id]);

                $moduleAvgCompletion[$module->title] = $query[0]->avg;

                if ($moduleAvgCompletion[$module->title] == null) {
                    $moduleAvgCompletion[$module->title] = 0;
                }
            } else {
                $moduleAvgCompletion[$module->title] = 0;
            }

        }

        foreach ($moduleM->lmsLessons as $lesson) {

            $lessonCount[$lesson->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Lesson' AND progress_id=? AND user_id IN (" . implode(",", $users) . ")", [$lesson->id]);
            $lessonCount[$lesson->title] = $lessonCount[$lesson->title][0]->counted;
            $lessonCount[$lesson->title] = $this->percentage($totalUsers, $lessonCount[$lesson->title]);

            $sessionId = $lesson->sessions->first()->id;

            $query = DB::select("SELECT CEIL(AVG(t3.diff)) as avg FROM (SELECT t1.user_id,TIMESTAMPDIFF(DAY,t1.st,t2.en) as diff FROM (SELECT created_at  as st,user_id FROM `progresses` WHERE (progress_type LIKE '%Session' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t1
INNER JOIN (SELECT created_at  as en,user_id FROM `progresses` WHERE (progress_type LIKE '%Lesson' AND progress_id=?) AND user_id IN (" . implode(",", $users) . ")) t2 ON t1.user_id = t2.user_id) t3", [$sessionId, $lesson->id]);

            $lessonAvgCompletion[$lesson->title] = $query[0]->avg;

            if ($lessonAvgCompletion[$lesson->title] == null) {
                $lessonAvgCompletion[$lesson->title] = 0;
            }
        }

        foreach ($lessonL->sessions as $session) {
            $sessionCount[$session->title] = DB::select("select COUNT(*) as counted from progresses where progress_type LIKE '%Session' AND progress_id=? AND user_id IN (".implode(",",$users).")",[$session->id]);
            $sessionCount[$session->title] = $sessionCount[$session->title][0]->counted;
            $sessionCount[$session->title] = $this->percentage($totalUsers,$sessionCount[$session->title]);
        }

        foreach ($moduleCount as $key => $value) {
            $modulePieChart[$key] = $this->percentage(array_sum($moduleCount), $value);
        }
        foreach ($lessonCount as $key => $value) {
            $lessonPieChart[$key] = $this->percentage(array_sum($lessonCount), $value);
        }
        foreach ($sessionCount as $key => $value) {
            $sessionPieChart[$key] = $this->percentage(array_sum($sessionCount), $value);
        }

        if($cohort) {
            $dataForCache[$course->id."-".$moduleM->id."-".$lessonL->id."-c".$cohort->id] = [
                $modulePieChart,
                $lessonPieChart,
                $sessionPieChart,
                $moduleAvgCompletion,
                $lessonAvgCompletion,
                $colorPallete,
                $moduleCount,
                $lessonCount,
                $sessionCount,
            ];
        } else {
            $dataForCache[$course->id."-".$moduleM->id."-".$lessonL->id] = [
                $modulePieChart,
                $lessonPieChart,
                $sessionPieChart,
                $moduleAvgCompletion,
                $lessonAvgCompletion,
                $colorPallete,
                $moduleCount,
                $lessonCount,
                $sessionCount,
            ];
        }


        foreach ($dataForCache as $key => $value) {
            Cache::forever($key, $value);
        }
    }

    protected function percentage($total, $portion)
    {
        if ($total == 0) {
            return 0;
        }

        return round(($portion / $total) * 100, 2);
    }
}
