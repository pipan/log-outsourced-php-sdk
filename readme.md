# Outsourced PHP SDK

[![Build Status](https://travis-ci.com/pipan/log-outsourced-php-sdk.svg?branch=master)](https://travis-ci.com/pipan/log-outsourced-php-sdk)

PHP SDK for [Outsourced](https://github.com/pipan/log-outsourced-api)

## Installation

`composer require outsourced/sdk`

## Usage

To do any communication with outsourced API, you have to create an `Outsourced` instance. We provide `HttpOutsourced` class. By default, this class uses `Guzzle` as a http client, but you can use other clients, if you want to.

```php
use OutsourcedSdk/HttpOutsourced;

$api = HttpOutsourced::makeWithGuzzle([
    'host' => 'https://outsourced.yourdomain.com',
    'accessKey' => 'project-access-key'
]);
```

### Single Log

Send single log to outsourced server by calling `logSingle` method of `HttpOutsourced` instance. 

```php
use OutsourcedSdk/HttpOutsourced;

$api = HttpOutsourced::makeWithGuzzle([
    'host' => 'https://outsourced.yourdomain.com',
    'accessKey' => 'project-access-key'
]);

$api->logSingle('level', 'message', ['context' => 'any additional values']);
```

### Batch log

Send logs in batch to outsourced server by calling `logBatch` method of `HttpOutsourced` instance. 

```php
use OutsourcedSdk/HttpOutsourced;

$api = HttpOutsourced::makeWithGuzzle([
    'host' => 'https://outsourced.yourdomain.com',
    'accessKey' => 'project-access-key'
]);

$api->logBatch([
    [
        'level' => 'info',
        'message' => 'log #1'
    ], [
        'level' => 'error',
        'message' => 'log #2',
        'context' => [
            'custom' => 'custom value'
        ]
    ]
]);
```

### Additional Context for All Logs

You may want to add some addition information to all your logs without the need to specify this context when calling `logSingle` or `logBatch`. You can define global context when creating HttpOutsourced instance. Just add `logging.context` index, to config array, with your values.

```php
use OutsourcedSdk/HttpOutsourced;

$api = HttpOutsourced::makeWithGuzzle([
    'host' => 'https://outsourced.yourdomain.com',
    'accessKey' => 'project-access-key',
    'logging' => [
        'context' => [
            'environment' => 'production'
        ]
    ]
]);
```

### Verify Permissions

Send permission verification request to outsourced server by calling `verifyPermissions` method of `HttpOutsourced` instance. 

```php
use OutsourcedSdk/HttpOutsourced;

$api = HttpOutsourced::makeWithGuzzle([
    'host' => 'https://outsourced.yourdomain.com',
    'accessKey' => 'project-access-key'
]);

$api->verifyPermissions('username', ['list', 'of', 'permissions']);
```

### Custom HttpClient

Your custom client have to implement `Psr\Http\Client\ClientInterface` interface. Then you can use instance of your class as a first parameter of `make` static method of `HttpOutsourced`.

```php
use OutsourcedSdk/HttpOutsourced;

$api = HttpOutsourced::make(new MyClient(), [
    'host' => 'https://outsourced.yourdomain.com',
    'accessKey' => 'project-access-key'
]);

$api->verifyPermissions('username', ['list', 'of', 'permissions']);
```

## Logging

Every log consists of 2 required properties and one optional property

* level - should be one of these 8 options `debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, `emergency`
* message - description of logged event
* context *optional - additional logged values

### Default Context

If you create `HttpOutsourced` instance by calling `make` or `makeWithGuzzle` you will automaticaly receive these values in every log context:

* url - current URL
* method - current request method
* ip - user IP
* user_agent - user agent name
* server - server software
* php - php version

### LoggerInterface

To use this SDK as a `PSR-3 logger` you can use our implementation of `LoggerInterface`, `OutsourcedLogger`.
