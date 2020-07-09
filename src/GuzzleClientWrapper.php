<?php

namespace LogOutsourcedSdk;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleClientWrapper implements ClientInterface
{
    private $client;

    public function __construct($config)
    {
        $this->client = new Client($config);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request);
    }
}