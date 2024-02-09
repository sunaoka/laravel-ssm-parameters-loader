<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersLoader\Tests;

use Illuminate\Foundation\Application;
use Sunaoka\LaravelSsmParametersLoader\ServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('ssm-parameters-loader', [
            'enable' => true,
            'ttl' => 0,
            'prefix' => 'ssm:',
            'ssm' => [
                'credentials' => [
                    'key' => 'key',
                    'secret' => 'secret',
                    'token' => null,
                ],
                'region' => 'us-east-1',
                'version' => 'latest',
                'endpoint' => null,
            ],
        ]);
    }
}
