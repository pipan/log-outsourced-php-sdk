<?php

namespace OutsourcedSdk;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleClientWrapper implements ClientInterface
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request);
    }
}