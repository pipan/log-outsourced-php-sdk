# Log Outsourced PHP SDK

[![Build Status](https://travis-ci.com/pipan/log-outsourced-php-sdk.svg?branch=master)](https://travis-ci.com/pipan/log-outsourced-php-sdk)

PHP SDK for Log Outsourced API

## Installation

`composer require outsourced/log-sdk`

## API

This SDK can be used for direct communication with your API service, `LogOutsourced`, or you can use this SDK as a `PSR-3 logger` with `LogOutsourcedLogger`

### LogOutsourced

If you want to call API directly, then you can use `LogOutsourced` interface to call `single` and `batch` API. We provide `HttpLogOutsourced` implemetation of this interface.

#### Create by facade

The easiest way to get a hold of `HttpLogOutsourced` instance is to use `LogOutsourcedFacade`

```php
use LogOutsourcedSdk/LogOutsourcedFacade;

$api = LogOutsourcedFacade::create('https://logoutsourced.url/logs/somehash');
```

#### Add custom context

You can add custom context value to all logs. Add an array with your values as a second parameter for `create` static method.

```php
use LogOutsourcedSdk/LogOutsourcedFacade;

$api = LogOutsourcedFacade::create('https://logoutsourced.url/logs/somehash', [
    'environment' => 'production'
]);
```

#### Custom http communication provider

`LogOutsourcedFacade::create` will create an http instance that communicates with API service by useing guzzle. If you want to use different http communication provider, you will have to create `HttpLogOutsourced` instace by yourself. `HttpLogOutsourced` accepts as a first argument `Psr\Http\Client\ClientInterface`.

```php
use LogOutsourcedSdk/HttpLogOutsourced;

$api = new HttpLogOutsourced(
    new CustomClient(), [
        'uri' => 'https://logoutsourced.url/logs/somehash',
        'context' => [
            'environment' => 'production'
        ]
    ]
);
```

### LoggerInterface

To use this SDK as a `PSR-3 logger` you can use our implementation of `LoggerInterface`, `LogOutsourcedLogger`.

#### Create by facade

The easiest way to get a hold of `LoggerInterface` instance is to use `LogOutsourcedFacade`

```php
use LogOutsourcedSdk/LogOutsourcedFacade;

$logger = LogOutsourcedFacade::createLogger('https://logoutsourced.url/logs/somehash');
```

#### Add custom context

see [add custom context](#add-custom-context) of LogOutsourced section.
