<?php

namespace OutsourcedSdk;

use OutsourcedSdk\Logging\LoggingRequestFactory;
use OutsourcedSdk\Permission\PermissionRequestFactory;
use Psr\Http\Client\ClientInterface;

class HttpOutsourced implements Outsourced
{
    private $client;
    private $loggingFactory;
    private $permissionFactory;

    public function __construct(ClientInterface $client, LoggingRequestFactory $loggingFactory, PermissionRequestFactory $permissionFactory)
    {
        $this->client = $client;
        $this->loggingFactory = $loggingFactory;
        $this->permissionFactory = $permissionFactory;
    }

    public static function make(ClientInterface $client, $config)
    {
        if (!isset($config['host'])) {
            throw new MissingValueException('Host config value is missing');
        }
        if (!isset($config['accessKey'])) {
            throw new MissingValueException('Host config value is missing');
        }

        $loggingContext = [
            'url' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'server' => $_SERVER['SERVER_SOFTWARE'],
            'runtime' => 'php' . phpversion()
        ];
        $loggingContext = array_merge($config['logging']['context'] ?? [], $loggingContext);

        return new HttpOutsourced($client,
            new LoggingRequestFactory($config['host'], $config['accessKey'], $loggingContext),
            new PermissionRequestFactory($config['host'], $config['accessKey'])
        );
    }

    public static function makeWithGuzzle($config)
    {
        return HttpOutsourced::make(new GuzzleClientWrapper(), $config);
    }

    public function logSingle($level, $message, $context = [])
    {
        return $this->client->sendRequest(
            $this->loggingFactory->createSingleLogRequest($level, $message, $context)
        );
    }

    public function logBatch($logs)
    {
        return $this->client->sendRequest(
            $this->loggingFactory->createBatchLogRequest($logs)
        );
    }

    public function verifyPermissions($user, $permissions)
    {
        return $this->client->sendRequest(
            $this->permissionFactory->createVerifyRequest($user, $permissions)
        );
    }
}