<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schedules = \App\Models\Schedule::all();
        foreach ($schedules as $schedule) {

            $schedule_type = $schedule->schedule_type;

            foreach ($schedule->lessons as $lesson) {
                $value = $lesson->getDripOrLockDays($schedule->id);

                foreach ($lesson->sessions as $session) {
                    if(count($schedule->sessions) == 0) {
                        $schedule->sessions()->attach($session);
                    }

                    if ($schedule_type === "dripped") {
                        DB::table('schedulables')
                            ->where([
                                ['schedule_id',$schedule->id],
                                ['schedulable_id',$session->id],
                                ['schedulable_type',"App\Models\Session"]
                            ])
                            ->update([
                                'drip_days' => $value
                            ]);
                    }
                    else {
                        DB::table('schedulables')
                            ->where([
                                ['schedule_id',$schedule->id],
                                ['schedulable_id',$session->id],
                                ['schedulable_type',"App\Models\Session"]
                            ])
                            ->update([
                                'lock_date' => date("Y-m-d H:i:s", strtotime($value))
                            ]);
                    }
                }

            }
        }
    }
}
