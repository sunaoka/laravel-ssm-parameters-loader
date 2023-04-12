<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersStore;

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
            __DIR__ . '/../config/ssm-parameters-store.php',
            'ssm-parameters-store'
        );

        /** @var Repository $config */
        $config = $this->app->make('config');

        if ($config->get('ssm-parameters-store.enable', false) !== true) {
            return;  // @codeCoverageIgnore
        }

        $ssm = new SsmService(
            new SsmClient((array)$config->get('ssm-parameters-store.ssm')),
            (int)$config->get('ssm-parameters-store.ttl', 0) // @phpstan-ignore-line
        );
        $ssm->loadParameters();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes(
            [__DIR__ . '/../config/ssm-parameters-store.php' => $this->app->configPath('ssm-parameters-store.php')],
            'ssm-parameters-store-config'
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
