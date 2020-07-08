<?php

namespace Tests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LogOutsourcedSdk\HttpLogOutsourced;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

final class HttpLogOutsourcedBatchTest extends TestCase
{
    public function testBatchResponseOk(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest');

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->batch([
            [
                'level' => 'info',
                'message' => 'message'
            ]
        ]);
    }

    public function testBatchRequestContainsJson(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                function (Request $request) {
                    $data = json_decode($request->getBody()->__toString(), true);
                    return isset($data[0])
                        && isset($data[0]['level']) && $data[0]['level'] == 'info'
                        && isset($data[0]['message']) && $data[0]['message'] == 'message';
                })
            );

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->batch([
            [
                'level' => 'info',
                'message' => 'message'
            ]
        ]);
    }

    public function testBatchRequestContainsJsonWithContext(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                function (Request $request) {
                    $data = json_decode($request->getBody()->__toString(), true);
                    return isset($data[0])
                        && isset($data[0]['level']) && $data[0]['level'] == 'info'
                        && isset($data[0]['message']) && $data[0]['message'] == 'message'
                        && isset($data[0]['context']) && $data[0]['context']['more'] == 'context';
                })
            );

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->batch([
            [
                'level' => 'info',
                'message' => 'message',
                'context' => ['more' => 'context']
            ]
        ]);
    }

    public function testBatchRequestContainsJsonWithContextAutomaticValues(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                function (Request $request) {
                    $data = json_decode($request->getBody()->__toString(), true);
                    $context = $data[0]['context'];
                    return $context['url'] == 'http://www.phpunit.test/uri'
                        && $context['method'] == 'GET'
                        && $context['ip'] == '127.0.0.1'
                        && $context['user_agent'] == 'phpunit'
                        && $context['server'] == 'phpunit'
                        && isset($context['php']);
                })
            );

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->batch([
            [
                'level' => 'info',
                'message' => 'message',
                'context' => ['more' => 'context']
            ]
        ]);
    }

    public function testBatchResponse404(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(404));

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->batch([
            [
                'level' => 'info',
                'message' => 'message'
            ]
        ]);
    }

    public function testBatchResponse500(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(500));

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->batch([
            [
                'level' => 'info',
                'message' => 'message'
            ]
        ]);
    }
}
