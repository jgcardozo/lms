<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Gamification\Gamification;

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
