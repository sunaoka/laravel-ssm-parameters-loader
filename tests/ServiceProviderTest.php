<?php

declare(strict_types=1);

namespace Sunaoka\LaravelSsmParametersLoader\Tests;

use Aws\MockHandler;
use Aws\Result;
use Sunaoka\LaravelSsmParametersLoader\ParametersLoader;

class ServiceProviderTest extends TestCase
{
    public function test_load_parameters(): void
    {
        $handler = new MockHandler();

        for ($i = 0; $i < 20; $i++) {
            putenv("ENV{$i}=ssm:/Path/To/Name{$i}");
            $parameters[] = [
                'Name' => "/Path/To/Name{$i}",
                'Type' => 'String',
                'Value' => "Value{$i}",
            ];
            if (count($parameters) === 10) {
                $handler->append(new Result([
                    'Parameters' => $parameters,
                    'InvalidParameters' => [],
                ]));
                $parameters = [];
            }
        }

        config()->set('ssm-parameters-loader.ssm.handler', $handler);

        $this->app->make(ParametersLoader::class)->load();  // @phpstan-ignore-line

        for ($i = 0; $i < 20; $i++) {
            self::assertSame("Value{$i}", getenv("ENV{$i}"));
            self::assertSame("Value{$i}", $_SERVER["ENV{$i}"]);
            self::assertSame("Value{$i}", $_ENV["ENV{$i}"]);
        }
    }
}
