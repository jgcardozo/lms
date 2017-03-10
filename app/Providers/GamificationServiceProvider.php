<?php

namespace App\Providers;

use App\Gamification\Gamification;
use Illuminate\Support\ServiceProvider;

class GamificationServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Gamification::class, function() {
			return new Gamification();
		});
    }
}