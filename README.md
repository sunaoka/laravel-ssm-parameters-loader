# Laravel SSM Parameters loader

[![Latest](https://poser.pugx.org/sunaoka/laravel-ssm-parameters-loader/v)](https://packagist.org/packages/sunaoka/laravel-ssm-parameters-loader)
[![License](https://poser.pugx.org/sunaoka/laravel-ssm-parameters-loader/license)](https://packagist.org/packages/sunaoka/laravel-ssm-parameters-loader)
[![PHP](https://img.shields.io/packagist/php-v/sunaoka/laravel-ssm-parameters-loader)](composer.json)
[![Laravel](https://img.shields.io/badge/laravel-9.x%20%7C%2010.x-red)](https://laravel.com/)
[![Test](https://github.com/sunaoka/laravel-ssm-parameters-loader/actions/workflows/test.yml/badge.svg)](https://github.com/sunaoka/laravel-ssm-parameters-loader/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/sunaoka/laravel-ssm-parameters-loader/branch/main/graph/badge.svg?token=PK3P6j6Jrz)](https://codecov.io/gh/sunaoka/laravel-ssm-parameters-loader)

----

Load values from AWS SSM Parameter store into environment variables for Laravel

## Installation

```bash
composer require sunaoka/laravel-ssm-parameters-loader
```

```bash
php artisan vendor:publish --tag=ssm-parameters-loader-config
```

## Usage

### Create a SSM Parameter

```bash
aws ssm put-parameter --name '/path/to/value' --type String --value 'my secret value'
```

### Set to .env

```dotenv
MY_PARAMETER=ssm:/path/to/value
```

### Example 1

```php
use Sunaoka\LaravelSsmParametersLoader\ParametersLoader;

echo env('MY_PARAMETER');
// still 'ssm:/path/to/value'

app()->make(ParametersLoader::class)->load();

echo env('MY_PARAMETER');
// my secret value
```

### Example 2

```php
use Sunaoka\LaravelSsmParametersLoader\ParametersLoader;

$result = app()->make(ParametersLoader::class)->getParameters();

var_dump($result);
// array(1) {
//   'MY_PARAMETER' =>
//   string(15) "my secret value"
// }
```
