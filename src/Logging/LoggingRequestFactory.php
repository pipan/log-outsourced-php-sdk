<?php

namespace OutsourcedSdk\Logging;

use OutsourcedSdk\Request\RequestBuilder;
use Psr\Http\Message\RequestInterface;

class LoggingRequestFactory
{
    private $context;
    private $singleUrl;
    private $batchUrl;

    public function __construct($host, $accessKey, $context = [])
    {
        $this->context = $context;
        $this->singleUrl = $host . '/logs/' . $accessKey;
        $this->batchUrl = $host . '/logs/' . $accessKey . '/batch';
    }

    public function createSingleLogRequest($level, $message, $context = []): RequestInterface
    {
        $body = [
            'level' => $level,
            'message' => $message,
            'context' => array_merge($context, $this->context)
        ];
        return (new RequestBuilder())
            ->withMethod('POST')
            ->withUrl($this->singleUrl)
            ->json($body)
            ->build();
    }

    public function createBatchLogRequest($logs): RequestInterface
    {
        $body = [];
        foreach ($logs as $log) {
            $log['context'] = array_merge($log['context'] ?? [], $this->context);
            $body[] = $log;
        }
        return (new RequestBuilder())
            ->withMethod('POST')
            ->withUrl($this->batchUrl)
            ->json($body)
            ->build();
    }
}