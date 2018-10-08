<?php

namespace Ubient\PwnedPasswords\Tests\Unit\Api;

use Ubient\PwnedPasswords\Api\ApiGateway;
use Ubient\PwnedPasswords\Tests\TestCase;
use Ubient\PwnedPasswords\Api\FakeApiGateway;
use Ubient\PwnedPasswords\Tests\Unit\Api\ApiGatewayContractTests;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class FakeApiGatewayTest extends TestCase
{
    use ApiGatewayContractTests;

    protected function getApiGateway(): ApiGateway
    {
        return new FakeApiGateway();
    }
}
