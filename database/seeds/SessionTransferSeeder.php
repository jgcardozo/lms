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
        $watched = DB::table('session_user')->select()->get();

        foreach ($watched as $w) {
            $p = new \App\Models\Progress;
            $p->user()->associate($w->user_id);
            $p->setCreatedAt($w->created_at);
            $p->save();
            \App\Models\Session::find($w->session_id)->progress()->save($p);
        }
    }
}
