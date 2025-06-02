<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersLoader;

use Aws\Ssm\SsmClient;

class ParametersLoader extends \Sunaoka\SsmParametersLoader\ParametersLoader
{
    public function __construct(
        SsmClient $ssmClient,
        private int $ttl,
        string $prefix,
    ) {
        parent::__construct($ssmClient, $prefix);
    }

    /**
     * @param  array<string, string>|null  $environments
     * @return array<string, string>
     *
     * @link https://docs.aws.amazon.com/systems-manager/latest/APIReference/API_GetParameters.html
     */
    public function getParameters(?string $prefix = null, ?array $environments = null): array
    {
        return cache()->remember('ssm-parameters-loader', $this->ttl, function () use ($prefix): array {
            return parent::getParameters($prefix);
        });
    }
}
