<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersLoader\Tests;

use Aws\MockHandler;
use Aws\Result;
use Aws\Ssm\SsmClient;
use RuntimeException;
use Sunaoka\LaravelSsmParametersLoader\ParametersLoader;

class ParametersLoaderTest extends TestCase
{
    public function test_load_parameters(): void
    {
        $handler = new MockHandler();

        for ($i = 0; $i < 20; ++$i) {
            putenv("ENV{$i}=ssm:/Path/To/Name{$i}");
            $parameters[] = [
                'Name'  => "/Path/To/Name{$i}",
                'Type'  => 'String',
                'Value' => "Value{$i}",
            ];
            if (count($parameters) === 10) {
                $handler->append(new Result([
                    'Parameters'        => $parameters,
                    'InvalidParameters' => [],
                ]));
                $parameters = [];
            }
        }

        $client = new SsmClient(config('ssm-parameters-loader.ssm') + ['handler' => $handler]);

        $loader = new ParametersLoader($client, 0);
        $loader->load();

        for ($i = 0; $i < 20; ++$i) {
            self::assertSame("Value{$i}", getenv("ENV{$i}"));
            self::assertSame("Value{$i}", $_SERVER["ENV{$i}"]);
            self::assertSame("Value{$i}", $_ENV["ENV{$i}"]);
        }
    }

    public function test_invalid_parameters(): void
    {
        $handler = new MockHandler();

        for ($i = 0; $i < 20; ++$i) {
            putenv("ENV{$i}=ssm:/Path/To/Name{$i}");
            $invalidParameters[] = "/Path/To/Name{$i}";
            if (count($invalidParameters) === 10) {
                $handler->append(new Result([
                    'Parameters'        => [],
                    'InvalidParameters' => $invalidParameters,
                ]));
                $invalidParameters = [];
            }
        }

        $client = new SsmClient(config('ssm-parameters-loader.ssm') + ['handler' => $handler]);

        $loader = new ParametersLoader($client, 0);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Invalid AWS Systems Manager parameter store names:/');

        $loader->load();
    }
}
