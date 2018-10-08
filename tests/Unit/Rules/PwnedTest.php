<?php

namespace Ubient\PwnedPasswords\Tests\Unit\Rules;

use Ubient\PwnedPasswords\Rules\Pwned;
use Illuminate\Support\Facades\Validator;
use Ubient\PwnedPasswords\Api\ApiGateway;
use Ubient\PwnedPasswords\Tests\TestCase;
use Ubient\PwnedPasswords\Api\FakeApiGateway;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class PwnedTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->app->instance(ApiGateway::class, new FakeApiGateway());
    }

    protected function passessRule(string $password, $threshold = null): bool
    {
        $rule = is_null($threshold)
            ? new Pwned()
            : new Pwned($threshold);

        return $rule->passes('attr', $password);
    }

    protected function passessValidator(string $password, $threshold = null): bool
    {
        $rule = is_null($threshold)
            ? 'pwned'
            : 'pwned:'.$threshold;

        return Validator::make(['attr' => $password], ['attr' => $rule])->passes();
    }

    /** @test */
    public function passwords_that_are_not_pwned_should_pass(): void
    {
        $safePassword = '0018A45C4D1DEF816A';

        $this->assertTrue($this->passessRule($safePassword));
        $this->assertTrue($this->passessValidator($safePassword));
    }

    /** @test */
    public function passwords_that_are_pwned_should_be_rejected(): void
    {
        $pwnedPassword = 'P@ssw0rd127';

        $this->assertFalse($this->passessValidator($pwnedPassword));
        $this->assertFalse($this->passessRule($pwnedPassword));
    }

    /** @test */
    public function passwords_that_are_pwned_and_are_below_the_threshold_should_pass(): void
    {
        $pwnedPassword = 'P@ssw0rd127';
        $threshold = 5;

        $this->assertTrue($this->passessValidator($pwnedPassword, $threshold));
        $this->assertTrue($this->passessRule($pwnedPassword, $threshold));
    }

    /** @test */
    public function passwords_that_are_pwned_and_match_the_threshold_should_be_rejected(): void
    {
        $pwnedPassword = 'hammertime6';
        $threshold = 5;

        $this->assertFalse($this->passessValidator($pwnedPassword, $threshold));
        $this->assertFalse($this->passessRule($pwnedPassword, $threshold));
    }

    /** @test */
    public function passwords_that_are_pwned_and_are_above_the_threshold_should_be_rejected(): void
    {
        $pwnedPassword = 'P@ssw0rd';
        $threshold = 5;

        $this->assertFalse($this->passessValidator($pwnedPassword, $threshold));
        $this->assertFalse($this->passessRule($pwnedPassword, $threshold));
    }

    /** @test */
    public function it_should_show_the_validation_error_message_when_used_as_a_rule_object(): void
    {
        $validator = Validator::make(['my-password' => 'P@ssw0rd'], [
            'my-password' => new Pwned(75),
        ]);

        $errorMessage = $validator->errors()->first();

        $this->assertEquals(
            'my-password was found in at least 75 prior security incident(s). Please choose a more secure password.',
            $errorMessage
        );
    }
}
