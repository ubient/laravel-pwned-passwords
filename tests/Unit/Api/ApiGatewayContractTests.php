<?php

namespace Ubient\PwnedPasswords\Tests\Unit\Api;

use Ubient\PwnedPasswords\Contracts\ApiGateway;

/**
 * This file exists as a bridge, creating a best-of-both-worlds situation:.
 *
 * - We cannot always query the real API (how would we get our tests to pass offline?)
 * - We cannot always trust the fake API (how would we know the real API still works?)
 */
trait ApiGatewayContractTests
{
    abstract protected function getApiGateway(): ApiGateway;

    /** @test */
    public function passwords_that_are_known_to_be_pwned_should_indicate_how_often_they_were_pwned(): void
    {
        $gateway = $this->getApiGateway();

        $occurrences = $gateway->search('P@ssw0rd');

        $this->assertTrue(is_int($occurrences));
        $this->assertTrue($occurrences >= 49938);
    }

    /** @test */
    public function passwords_that_are_not_pwned_should_indicate_zero_results(): void
    {
        $gateway = $this->getApiGateway();
        $fakePassword = uniqid().'0018A45C4D1DEF816A';

        $occurrences = $gateway->search($fakePassword);

        $this->assertEquals(0, $occurrences);
    }

    /** @test */
    public function an_empty_password_should_indicate_zero_results(): void
    {
        $gateway = $this->getApiGateway();

        $occurrences = $gateway->search('');

        $this->assertEquals(0, $occurrences);
    }
}
