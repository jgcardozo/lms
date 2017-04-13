<?php

namespace App\Providers;

use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Custom validation rule for [old password]
         */
        Validator::extend('oldpassword', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, Auth::user()->password);
        });

        Schema::defaultStringLength(191);

        // \StephaneCoinon\Papertrail\Laravel5::boot();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
