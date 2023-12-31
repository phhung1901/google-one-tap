<?php
namespace GoogleOneTap\Services;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class GoogleOneTapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(){
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'google_one_tap');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/google-one-tap.php' => config_path('google-one-tap.php'),
            ], 'google_one_tap');

            $this->publishes([
                __DIR__.'/../../resources/views/components/' => resource_path('views/components/google_one_tap'),
            ], 'google_one_tap');
        }

        $socialite = $this->app->make(Factory::class);

        $socialite->extend(
            'google-one-tap',
            fn() => $socialite->buildProvider(GoogleOneTap::class, config('services.google')),
        );
    }

    /**
     * Register the application services.
     */
    public function register()
    {

    }
}
