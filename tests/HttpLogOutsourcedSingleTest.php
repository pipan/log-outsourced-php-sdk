<?php

namespace Tests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LogOutsourcedSdk\HttpLogOutsourced;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

final class HttpLogOutsourcedSingleTest extends TestCase
{
    public function testSingleResponseOk(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest');

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->single('info', 'message');
    }

    public function testSingleRequestContainsJson(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                function (Request $request) {
                    $data = json_decode($request->getBody()->__toString(), true);
                    return isset($data['level']) && $data['level'] == 'info'
                        && isset($data['message']) && $data['message'] == 'message'
                        && isset($data['context']);
                })
            );

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->single('info', 'message');
    }

    public function testSingleRequestContainsJsonWithContext(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                function (Request $request) {
                    $data = json_decode($request->getBody()->__toString(), true);
                    return isset($data['level']) && $data['level'] == 'info'
                        && isset($data['message']) && $data['message'] == 'message'
                        && isset($data['context']) && $data['context']['more'] == 'context';
                })
            );

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->single('info', 'message', ['more' => 'context']);
    }

    public function testSingleRequestContainsJsonWithContextAutomaticValues(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(
                function (Request $request) {
                    $data = json_decode($request->getBody()->__toString(), true);
                    $context = $data['context'];
                    return $context['url'] == 'http://www.phpunit.test/uri'
                        && $context['method'] == 'GET'
                        && $context['ip'] == '127.0.0.1'
                        && $context['user_agent'] == 'phpunit'
                        && $context['server'] == 'phpunit'
                        && isset($context['php']);
                })
            );

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->single('info', 'message', ['more' => 'context']);
    }

    public function testSingleResponse404(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(404));

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->single('info', 'message');
    }

    public function testSingleResponse500(): void
    {
        /** @var ClientInterface|MockObject */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(500));

        $api = new HttpLogOutsourced($client, ['uri' => '']);
        $api->single('info', 'message');
    }
}
