<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $watched = DB::table('session_user')->get();

        $data = [];

        foreach ($watched as $w) {
           $data[] = [
           'user_id' => $w->user_id,
           'created_at' => $w->created_at,
           'progress_type' => 'App\Models\Session',
           'progress_id' => $w->session_id
           ];
        }

        $data = collect($data);

        $chunkedData = $data->chunk(5000);
        foreach ($chunkedData as $sessions) {
            DB::table('progresses')->insert($sessions->toArray());
        }

    }
}
