<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersLoader;

use Aws\Ssm\SsmClient;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;

class ServiceProvider extends \Illuminate\Support\ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ssm-parameters-loader.php',
            'ssm-parameters-loader'
        );

        if (config('ssm-parameters-loader.enable', true) !== true) {
            return;  // @codeCoverageIgnore
        }

        $this->app->singleton(ParametersLoader::class, function ($app) {
            return new ParametersLoader(
                new SsmClient((array)config('ssm-parameters-loader.ssm')),
                (int)config('ssm-parameters-loader.ttl', 0)  // @phpstan-ignore-line
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->publishes(
            [__DIR__ . '/../config/ssm-parameters-loader.php' => config_path('ssm-parameters-loader.php')],
            'ssm-parameters-loader-config'
        );

        if (config('ssm-parameters-loader.enable', true) &&
            array_filter(config('ssm-parameters-loader.ssm.credentials', [])) !== [])  // @phpstan-ignore-line
        {
            $this->app->make(ParametersLoader::class)->load();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return class-string[]
     */
    public function provides(): array
    {
        return [ParametersLoader::class];
    }
}
