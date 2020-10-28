<?php

namespace OutsourcedSdk\Request;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class RequestBuilder
{
    private $method;
    private $body;
    private $headers;
    private $url;

    public function __construct()
    {
        $this->method = 'GET';
        $this->body = '';
        $this->headers = [];
        $this->url = '';
    }

    public function withMethod($value): RequestBuilder
    {
        $this->method = $value;
        return $this;
    }

    public function json($body = []): RequestBuilder
    {
        $this->body = json_encode($body);
        return $this->withHeader('content-type', 'application/json');
    }

    public function withHeader($key, $value): RequestBuilder
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withUrl($value): RequestBuilder
    {
        $this->url = $value;
        return $this;
    }

    public function build(): RequestInterface {
        return new Request($this->method, $this->url, $this->headers, $this->body);
    }
}