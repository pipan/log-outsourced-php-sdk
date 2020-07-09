<?php

namespace LogOutsourcedSdk;

use Psr\Log\LoggerInterface;

class LogOutsourcedFacade
{
    public static function create($uri, $context = [])
    {
        return new HttpLogOutsourced(
            new GuzzleClientWrapper([
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
