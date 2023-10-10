<?php
namespace GoogleOneTap\Services;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Laravel\Socialite\Contracts\Factory;

class GoogleOneTapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(){
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'google_one_tap');

        $socialite = $this->app->make(Factory::class);

        $socialite->extend(
            'google-one-tap',
            fn() => $socialite->buildProvider(GoogleOneTap::class, config('services.google')),
        );

        Blade::directive('googleOneTapScript', function () {
            $tag = '<script src="https://accounts.google.com/gsi/client" async defer></script>';
            return "<?php echo '".$tag."'.PHP_EOL ?>";
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {

    }
}
