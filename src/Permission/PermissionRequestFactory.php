<?php

namespace OutsourcedSdk\Permission;

use OutsourcedSdk\Request\RequestBuilder;
use Psr\Http\Message\RequestInterface;

class PermissionRequestFactory
{
    private $verifyUrl;

    public function __construct($host, $accessKey)
    {
        $this->verifyUrl = $host . '/permissions/' . $accessKey;
    }

    public function createVerifyRequest($user, $permissions): RequestInterface
    {
        $query = http_build_query([
            'user' => $user,
            'permissions' => $permissions
        ]);
        return (new RequestBuilder())
            ->withMethod('GET')
            ->withUrl($this->verifyUrl . '?' . $query)
            ->json()
            ->build();
    }
}