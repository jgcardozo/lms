<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FillLessonCompletionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = \App\Models\Module::with('lmsLessons.sessions.progress')->get();

        foreach ( $modules as $module) {
            if($module->lmsLessons->count() == 0) {
                continue;
            }

            foreach ($module->lmsLessons as $lesson) {

                if($lesson->sessions->count() == 0) {
                    continue;
                }

                $sessionsIds = $lesson->sessions->pluck('id')->toArray();

                $userIds = DB::select("SELECT user_id FROM `progresses` WHERE progress_type LIKE '%Session' AND progress_id IN (".implode(", ",$sessionsIds).") GROUP BY user_id HAVING COUNT(DISTINCT progress_id) = ?",[count($sessionsIds)]);

                $userIds = json_decode(json_encode($userIds),true);

                $userIds = array_flatten($userIds);

                $data = [];

                foreach ($userIds as $userId) {
                    $data[] = [
                        'user_id' => $userId,
                        'progress_type' =>  'App\Models\Lesson',
                        'progress_id' => $lesson->id,
                        'created_at' => $lesson->sessions->last()->progress->firstWhere('user_id',$userId)->created_at,
                        'updated_at' => now()
                    ];
                }

                \App\Models\Progress::insert($data);
            }
        }
    }
}