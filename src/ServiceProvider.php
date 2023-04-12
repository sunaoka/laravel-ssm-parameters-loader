<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersLoader;

use Aws\Ssm\SsmClient;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\DeferrableProvider;

class ServiceProvider extends \Illuminate\Support\ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ssm-parameters-loader.php',
            'ssm-parameters-loader'
        );

        /** @var Repository $config */
        $config = $this->app->make('config');

        if ($config->get('ssm-parameters-loader.enable', false) !== true) {
            return;  // @codeCoverageIgnore
        }

        $ssm = new ParametersLoader(
            new SsmClient((array)$config->get('ssm-parameters-loader.ssm')),
            (int)$config->get('ssm-parameters-loader.ttl', 0) // @phpstan-ignore-line
        );
        $ssm->loadParameters();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes(
            [__DIR__ . '/../config/ssm-parameters-loader.php' => $this->app->configPath('ssm-parameters-loader.php')],
            'ssm-parameters-loader-config'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return class-string[]
     */
    public function provides(): array
    {
        return [SsmClient::class];
    }
}
