<?php

namespace LogOutsourcedSdk;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class HttpLogOutsourced implements LogOutsourced
{
    private $client;
    private $uri;
    private $context;

    public function __construct(ClientInterface $client, $config)
    {
        $this->client = $client;
        $this->uri = $config['uri'];
        $this->context = $config['context'] ?? [];
    }

    public function single($level, $message, $context = [])
    {
        $body = $this->applyMetaData([
            'level' => $level,
            'message' => $message,
            'context' => $this->applyMetaData($context)
        ]);
        $this->send($this->uri, $body);
    }

    public function batch($logs)
    {
        $body = [];
        foreach ($logs as $key => $log) {
            $body[$key] = $log;
            $body[$key]['context'] = $this->applyMetaData($log['context'] ?? []);
        }
        $this->send($this->uri . '/batch', $body);
    }

    private function applyMetaData($data)
    {
        return $data + $this->context + [
            'url' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'server' => $_SERVER['SERVER_SOFTWARE'],
            'php' => phpversion()
        ];
    }

    private function send($uri, $data)
    {
        $headers = [
            'content-type' => 'appication/json'
        ];
        $request = new Request('POST', $uri, $headers, json_encode($data));
        $this->client->sendRequest($request);
    }
}