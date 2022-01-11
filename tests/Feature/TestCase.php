<?php

namespace Tests\Feature;

use EllGreen\LaravelLoadFile\Laravel\Providers\LaravelLoadFileServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PDO;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelLoadFileServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('database.default', 'mysql');

        $config->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'database'),
            'database' => 'loadfile',
            'username' => 'loadfile',
            'password' => 'loadfile-testing',
            'options' => [
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            ],
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(realpath(__DIR__ . '/../migrations'));
    }
}
