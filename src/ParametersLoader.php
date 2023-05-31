<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersLoader;

use Aws\Ssm\SsmClient;
use RuntimeException;

class ParametersLoader
{
    public function __construct(
        private SsmClient $ssmClient,
        private int $ttl,
        private string $prefix,
    ) {
    }

    public function load(string $prefix = null): void
    {
        $parameters = $this->getParameters($prefix ?? $this->prefix);

        foreach ($parameters as $key => $value) {
            $_SERVER[$key] = $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    /**
     * @return array<string, string>
     *
     * @link https://docs.aws.amazon.com/systems-manager/latest/APIReference/API_GetParameters.html
     */
    public function getParameters(string $prefix = null): array
    {
        return cache()->remember('ssm-parameters-loader', $this->ttl, function () use ($prefix): array {  // @phpstan-ignore-line
            $environments = $this->getEnvironments($prefix ?? $this->prefix);

            $parameters = [];
            $invalidParameters = [];

            // Maximum number of 10 items.
            foreach (array_chunk($environments, 10, true) as $names) {
                /** @var array{Parameters: array<int, array{Name: string, Value: string}>, InvalidParameters: string[]} $result */
                $result = $this->ssmClient->getParameters([
                    'Names'          => array_values($names),
                    'WithDecryption' => true,
                ]);
                foreach ($result['Parameters'] as $parameter) {
                    $name = array_search($parameter['Name'], $environments, true);
                    $parameters[$name] = $parameter['Value'];
                }

                $invalidParameters[] = $result['InvalidParameters'];
            }

            $invalidParameters = array_merge(...$invalidParameters);
            if (count($invalidParameters) > 0) {
                throw new RuntimeException('Invalid AWS Systems Manager parameter store names: ' . implode(', ', $invalidParameters));
            }

            return $parameters;
        });
    }

    /**
     * @return array<string, string>
     */
    protected function getEnvironments(string $prefix): array
    {
        $environments = getenv(local_only: true);  // @phpstan-ignore-line
        if (!is_array($environments)) {
            return [];  // @codeCoverageIgnore
        }

        $environments = array_filter($environments, static fn(string $value): bool => str_starts_with($value, $prefix));
        if (count($environments) === 0) {
            return [];
        }

        return array_map(static fn(string $value): string => substr($value, strlen($prefix)), $environments);
    }
}
