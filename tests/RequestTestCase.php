<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class RequestTestCase extends TestCase
{
    public function assertUri($expected, UriInterface $uri)
    {
        $query = $uri->getQuery();
        if ($query) {
            $query = '?' . $query;
        }
        $uriCombine = $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . $query;
        $this->assertEquals($expected, $uriCombine);
    }

    public function assertRequest($expected, RequestInterface $request)
    {
        $this->assertEquals($expected['contentType'], $request->getHeaderLine('content-type'));
        $this->assertEquals($expected['method'], $request->getMethod());
        $this->assertUri($expected['uri'], $request->getUri());
    }

    protected function defaultContextWith($context = [])
    {
        return array_merge($context, [
            'url' => 'http://www.phpunit.test/uri',
            'method' => 'GET',
            'ip' => '127.0.0.1',
            'user_agent' => 'phpunit',
            'server' => 'phpunit',
            'php' => phpversion()
        ]);
    }
}
