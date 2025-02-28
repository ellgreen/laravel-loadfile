<?php

namespace EllGreen\LaravelLoadFile\Laravel\Providers;

use EllGreen\LaravelLoadFile\Builder\Builder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;

class LaravelLoadFileServiceProvider extends ServiceProvider
{
    /**
     * @psalm-suppress MixedArgument
     */
    public function register(): void
    {
        $this->app->bind(Builder::class, function (Application $app) {
            return new Builder($app->make(DatabaseManager::class));
        });

        $this->app->alias(Builder::class, 'load-file');
    }
}
