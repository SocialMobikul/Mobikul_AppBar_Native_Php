<?php

declare(strict_types=1);

namespace MobikulAppBar;

use Illuminate\Support\ServiceProvider;

class MobikulAppBarServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/mobikul_appbar.php' => config_path('mobikul_appbar.php'),
        ], 'mobikul-app-bar-config');

        $this->publishes([
            __DIR__ . '/../assets' => public_path('vendor/mobikul_appbar'),
        ], 'mobikul-app-bar-assets');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mobikul_appbar.php', 'mobikul_appbar');

        $this->app->singleton('mobikul-app-bar', function (): MobikulAppBarPlugin {
            return new MobikulAppBarPlugin(
                defaultOptions: (array) config('mobikul_appbar.defaults', [])
            );
        });
    }
}
