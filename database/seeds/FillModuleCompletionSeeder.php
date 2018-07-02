<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FillModuleCompletionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = \App\Models\Module::with('lmsLessons.progress')->get();

        foreach ( $modules as $module) {
            if($module->lmsLessons->count() == 0) {
                continue;
            }

            $lessonIds = $module->lmsLessons->pluck('id')->toArray();

            $userIds = DB::select("SELECT user_id FROM `progresses` WHERE progress_type LIKE '%Lesson' AND progress_id IN (".implode(", ",$lessonIds).") GROUP BY user_id HAVING COUNT(DISTINCT progress_id) = ?",[count($lessonIds)]);

            $userIds = json_decode(json_encode($userIds),true);

            $userIds = array_flatten($userIds);

            $data = [];

            foreach ($userIds as $userId) {
                $data[] = [
                    'user_id' => $userId,
                    'progress_type' =>  'App\Models\Module',
                    'progress_id' => $module->id,
                    'created_at' => $module->lmsLessons->last()->progress->firstWhere('user_id',$userId)->created_at,
                    'updated_at' => now()
                ];
            }

            \App\Models\Progress::insert($data);

        }
    }
}
