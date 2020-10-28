<?php

namespace Tests;

use OutsourcedSdk\HttpOutsourced;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class PermissionVerifyTest extends RequestTestCase
{
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
                    'method' => 'GET',
                    'uri' => 'http://example.host/permissions/key-0123?user=user&permissions%5B0%5D=content.view'
                ], $request);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->verifyPermissions('user', ['content.view']);
    }

    public function testRequestMultiplePermissions(): void
    {
        $self = $this;
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($self) {
                $self->assertRequest([
                    'contentType' => 'application/json',
                    'method' => 'GET',
                    'uri' => 'http://example.host/permissions/key-0123?user=user&permissions%5B0%5D=content.view&permissions%5B1%5D=content.create'
                ], $request);
                return true;
            }));

        $api = HttpOutsourced::make($client, [
            'host' => 'http://example.host',
            'accessKey' => 'key-0123'
        ]);
        $api->verifyPermissions('user', ['content.view', 'content.create']);
    }
}
