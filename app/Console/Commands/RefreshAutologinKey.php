<?php

namespace App\Console\Commands;

use Autologin;
use Illuminate\Console\Command;

class RefreshAutologinKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autologin:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh autologin key.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Autologin::refreshKey();
    }
}
