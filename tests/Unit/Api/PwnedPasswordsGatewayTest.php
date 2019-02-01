<?php

namespace Ubient\PwnedPasswords\Tests\Unit\Api;

use Ubient\PwnedPasswords\Api\ApiGateway;
use Ubient\PwnedPasswords\Tests\TestCase;
use Ubient\PwnedPasswords\Api\PwnedPasswordsGateway;

class PwnedPasswordsGatewayTest extends TestCase
{
    use ApiGatewayContractTests;

    protected function getApiGateway(): ApiGateway
    {
        return new PwnedPasswordsGateway();
    }
}
