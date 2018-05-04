<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (App\Models\Course::all() as $course) {
            $schedule = new App\Models\Schedule;
            $schedule->name = "Default";
            $schedule->course_id = $course->id;
            $schedule->schedule_type = "dripped";
            $schedule->save();

            $modules = $course->modules;

            foreach ($modules as $module) {
                $schedule->modules()->attach($module);


                DB::table('schedulables')
                    ->where([
                        ['schedule_id', $schedule->id],
                        ['schedulable_id', $module->id],
                        ['schedulable_type', "App\Models\Module"]
                    ])
                    ->update([
                        'drip_days' => 1
                    ]);

                $lessons = $module->lessons;

                if (!empty($lessons)) {
                    foreach ($lessons as $lesson) {
                        $schedule->lessons()->attach($lesson);


                        DB::table('schedulables')
                            ->where([
                                ['schedule_id', $schedule->id],
                                ['schedulable_id', $lesson->id],
                                ['schedulable_type', "App\Models\Lesson"]
                            ])
                            ->update([
                                'drip_days' => 1
                            ]);

                    }
                }
            }
        }
    }
}
