<?php

namespace Ubient\PwnedPasswords\Tests\Unit\Api;

use Ubient\PwnedPasswords\Api\PwnedPasswordsGateway;
use Ubient\PwnedPasswords\Contracts\ApiGateway;
use Ubient\PwnedPasswords\Tests\TestCase;

class PwnedPasswordsGatewayTest extends TestCase
{
    use ApiGatewayContractTests;

    protected function getApiGateway(): ApiGateway
    {
        return new PwnedPasswordsGateway();
    }
}
