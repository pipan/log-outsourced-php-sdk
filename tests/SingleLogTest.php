<?php

namespace Tests;

use Exception;
use OutsourcedSdk\HttpOutsourced;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class SingleLogTest extends RequestTestCase
{
    public function testMissingHost(): void
    {
        $this->expectException(Exception::class);
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $api = HttpOutsourced::make($client, [
            'accessKey' => 'key-0123'
        ]);
        $api->logSingle('info', 'message');
    }

    public function testMissingAccessKey(): void
    {
        $this->expectException(Exception::class);
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
        ]);
        $api->logSingle('info', 'message');
    }

    public function testRequest(): void
    {
        $self = $this;
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($self) {
                $self->assertRequest([
                    'contentType' => 'application/json',
                    'method' => 'POST',
                    'uri' => 'http://example.host/logs/key-0123'
                ], $request);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->logSingle('info', 'message');
    }

    public function testRequestBody(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $body = json_decode($request->getBody()->getContents(), true);
                $this->assertEquals([
                    'level' => 'info',
                    'message' => 'message',
                    'context' => $this->defaultContextWith()
                ], $body);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->logSingle('info', 'message');
    }

    public function testRequestBodyWithLogContext(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $body = json_decode($request->getBody()->getContents(), true);
                $this->assertEquals([
                    'level' => 'info',
                    'message' => 'message',
                    'context' => $this->defaultContextWith([
                        'custom' => 'custom-value'
                    ])
                ], $body);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->logSingle('info', 'message', [
            'custom' => 'custom-value'
        ]);
    }

    public function testRequestBodyWithConfigContext(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $body = json_decode($request->getBody()->getContents(), true);
                $this->assertEquals([
                    'level' => 'info',
                    'message' => 'message',
                    'context' => $this->defaultContextWith([
                        'custom' => 'custom-value'
                    ])
                ], $body);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123',
            'logging' => [
                'context' => [
                    'custom' => 'custom-value'
                ]
            ]
        ]);
        $api->logSingle('info', 'message');
    }
}
