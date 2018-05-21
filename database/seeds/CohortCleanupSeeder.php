<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CohortCleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userWithTags = DB::table('tag_user')
            ->select()
            ->where('tag_id','=',3786)
            ->get()->pluck('user_id')->toArray();
        $users = \App\Models\User::all()->pluck('id')->toArray();

        $userIds = array_diff($users,$userWithTags);

        foreach ($userIds as $id) {
            $user = \App\Models\User::find($id);
            $user->cohorts()->detach();
        }
    }
}
