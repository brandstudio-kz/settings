<?php

namespace BrandStudio\Settings;

use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{


    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/settings.php', 'settings');

        if ($this->app->runningInConsole()) {
            $this->publish();
        }

        if (config('settings.use_backpack')) {
            $this->loadRoutesFrom(__DIR__.'/routes/settings.php');
        }

    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'brandstudio');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'settings');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/database/migrations');
            $this->publish();
        }

    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/config/settings.php' => config_path('settings.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/resources/views/settings'      => resource_path('views/vendor/brandstudio/settings')
        ], 'views');

        $this->publishes([
            __DIR__.'/resources/lang'      => resource_path('lang/vendor/settings')
        ], 'lang');
    }

}
