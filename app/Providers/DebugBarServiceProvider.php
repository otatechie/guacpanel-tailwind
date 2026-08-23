<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DebugBarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
         * Sets third party service providers that are only needed on local/testing environments
         */
        if ($this->app->isLocal()) {
            /**
             * Loader for registering facades.
             */
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();

            /*
             * Load third party local providers
             */
            $this->app->register(\Fruitcake\LaravelDebugbar\ServiceProvider::class);

            /*
             * Load third party local aliases
             */
            $loader->alias('Debugbar', \Fruitcake\LaravelDebugbar\Facades\Debugbar::class);
        }
    }
}
