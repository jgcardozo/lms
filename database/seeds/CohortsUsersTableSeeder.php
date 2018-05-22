<?php

use Illuminate\Database\Seeder;

class CohortsUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (\App\Models\User::all() as $user) {
            $user->cohorts()->attach($user->cohort_id);
        }
    }
}
