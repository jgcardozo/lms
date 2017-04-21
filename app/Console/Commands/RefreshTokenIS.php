<?php

namespace App\Console\Commands;

use InfusionsoftFlow;
use Illuminate\Console\Command;

class RefreshTokenIS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'is:refreshtoken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Infusionsoft Token';

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
        InfusionsoftFlow::refreshToken();
    }
}
