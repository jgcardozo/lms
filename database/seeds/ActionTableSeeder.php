<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $actions = [
        'started',
        'completed',
        'clicked',
        'changed',
        'updated',
        'logged in',
        'posted to',
        'downloaded',
        'passed',
        'passed (retaken)',
        'failed',
        'failed (retaken)',
        'deleted',
        'created'
    ];

    public function run()
    {
        foreach ($this->actions as $action) {
        DB::table('actions')
            ->insert([
                'name' => $action,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
