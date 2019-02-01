<?php

namespace Ubient\PwnedPasswords\Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Ubient\PwnedPasswords\Tests\TestCase;

class PwnedPasswordsServiceProviderTest extends TestCase
{
    /** @test */
    public function it_should_register_the_validation_error_message_for_our_pwned_rule_with_the_validator(): void
    {
        $errorMessage = Validator::make(['attribute' => 'P@ssw0rd'], ['attribute' => 'pwned:23'])->errors()->first();

        $this->assertEquals(
            'attribute was found in at least 23 prior security incident(s). Please choose a more secure password.',
            $errorMessage
        );
    }
}
