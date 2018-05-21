<?php

use Illuminate\Database\Seeder;

class CohortCleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::all()->chunk('100');
        foreach ($users as $user) {
            foreach ($user as $u) {
                if (! $u->hasTag(3786)) {
                    $u->cohorts()->detach();
                }
            }
        }
    }
}
