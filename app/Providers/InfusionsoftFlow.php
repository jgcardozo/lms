<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class InfusionsoftFlow extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
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
        $this->app->singleton('isflow', function ($app) {
			return new \App\InfusionsoftFlow\InfusionsoftFlow(app('infusionsoft'));
		});
    }
}
