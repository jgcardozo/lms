<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $activities = [
        'Facebook',
        'Survey',
        'Billing details',
        'Password',
        'Contact details',
        'Quiz',
        'Admin',
        'Facebook Group',
        'Apply Event'
    ];

    public function run()
    {
        foreach ($this->activities as $activity) {
            DB::table('activities')
                ->insert([
                    'name' => $activity,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }
    }
}
