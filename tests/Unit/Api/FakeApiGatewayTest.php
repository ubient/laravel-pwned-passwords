<?php

namespace Ubient\PwnedPasswords\Tests\Unit\Api;

use Ubient\PwnedPasswords\Api\FakeApiGateway;
use Ubient\PwnedPasswords\Contracts\ApiGateway;
use Ubient\PwnedPasswords\Tests\TestCase;

class FakeApiGatewayTest extends TestCase
{
    use ApiGatewayContractTests;

    protected function getApiGateway(): ApiGateway
    {
        return new FakeApiGateway();
    }
}
