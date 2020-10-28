<?php

namespace Tests;

use OutsourcedSdk\HttpOutsourced;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class BatchLogTest extends RequestTestCase
{
    public function testRequst(): void
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
                    'uri' => 'http://example.host/logs/key-0123/batch'
                ], $request);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->logBatch([
            [
                'level' => 'info',
                'message' => 'message'
            ]
        ]);
    }

    public function testRequestBody(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $body = json_decode($request->getBody()->getContents(), true);
                $this->assertCount(1, $body);
                $this->assertEquals([
                    'level' => 'info',
                    'message' => 'message',
                    'context' => $this->defaultContextWith()
                ], $body[0]);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->logBatch([[
            'level' => 'info',
            'message' => 'message'
        ]]);
    }

    public function testRequestBodyWithLogContext(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                $body = json_decode($request->getBody()->getContents(), true);
                $this->assertCount(2, $body);
                $this->assertEquals([
                    'level' => 'info',
                    'message' => 'message',
                    'context' => $this->defaultContextWith([
                        'custom' => 'custom-value'
                    ])
                ], $body[0]);
                $this->assertEquals([
                    'level' => 'error',
                    'message' => 'message2',
                    'context' => $this->defaultContextWith()
                ], $body[1]);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->logBatch([
            [
                'level' => 'info',
                'message' => 'message',
                'context' => [
                    'custom' => 'custom-value'
                ]
            ], [
                'level' => 'error',
                'message' => 'message2'
            ]
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
                $this->assertCount(2, $body);
                $this->assertEquals([
                    'level' => 'info',
                    'message' => 'message',
                    'context' => $this->defaultContextWith([
                        'custom' => 'custom-value'
                    ])
                ], $body[0]);
                $this->assertEquals([
                    'level' => 'error',
                    'message' => 'message2',
                    'context' => $this->defaultContextWith([
                        'custom' => 'custom-value'
                    ])
                ], $body[1]);
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
        $api->logBatch([
            [
                'level' => 'info',
                'message' => 'message'
            ], [
                'level' => 'error',
                'message' => 'message2'
            ]
        ]);
    }
}
