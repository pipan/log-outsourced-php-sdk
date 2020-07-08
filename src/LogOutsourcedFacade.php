<?php

namespace LogOutsourcedSdk;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class LogOutsourcedFacade
{
    public static function create($uri, $context = [])
    {
        return new HttpLogOutsourced(
            new Client([
                'base' => $uri
            ]), [
                'uri' => $uri,
                'context' => $context
            ]
        );
    }

    public static function createLogger($uri, $context = []): LoggerInterface
    {
        return new LogOutsourcedLogger(
            LogOutsourcedFacade::create($uri, $context)
        );
    }
}
