<?php

namespace Ubient\PwnedPasswords\Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Ubient\PwnedPasswords\Api\FakeApiGateway;
use Ubient\PwnedPasswords\Contracts\ApiGateway;
use Ubient\PwnedPasswords\Rules\Pwned;
use Ubient\PwnedPasswords\Tests\TestCase;

class PwnedTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app->instance(ApiGateway::class, new FakeApiGateway());
    }

    protected function passesRule(string $password, $threshold = null): bool
    {
        $rule = is_null($threshold)
            ? new Pwned()
            : new Pwned($threshold);

        return $rule->passes('attr', $password);
    }

    protected function passesValidator(string $password, $threshold = null): bool
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

        $this->assertTrue($this->passesRule($safePassword));
        $this->assertTrue($this->passesValidator($safePassword));
    }

    /** @test */
    public function passwords_that_are_pwned_should_be_rejected(): void
    {
        $pwnedPassword = 'P@ssw0rd127';

        $this->assertFalse($this->passesValidator($pwnedPassword));
        $this->assertFalse($this->passesRule($pwnedPassword));
    }

    /** @test */
    public function passwords_that_are_pwned_and_are_below_the_threshold_should_pass(): void
    {
        $pwnedPassword = 'P@ssw0rd127';
        $threshold = 5;

        $this->assertTrue($this->passesValidator($pwnedPassword, $threshold));
        $this->assertTrue($this->passesRule($pwnedPassword, $threshold));
    }

    /** @test */
    public function passwords_that_are_pwned_and_match_the_threshold_should_be_rejected(): void
    {
        $pwnedPassword = 'hammertime6';
        $threshold = 5;

        $this->assertFalse($this->passesValidator($pwnedPassword, $threshold));
        $this->assertFalse($this->passesRule($pwnedPassword, $threshold));
    }

    /** @test */
    public function passwords_that_are_pwned_and_are_above_the_threshold_should_be_rejected(): void
    {
        $pwnedPassword = 'P@ssw0rd';
        $threshold = 5;

        $this->assertFalse($this->passesValidator($pwnedPassword, $threshold));
        $this->assertFalse($this->passesRule($pwnedPassword, $threshold));
    }

    /** @test */
    public function it_should_show_the_validation_error_message_when_used_as_a_rule_object(): void
    {
        $input = ['my-password' => 'P@ssw0rd'];
        $rules = ['my-password' => new Pwned(75)];

        $errorMessage = Validator::make($input, $rules)->errors()->first();

        $this->assertEquals(
            'my-password was found in at least 49938 prior security incident(s). Please choose a more secure password.',
            $errorMessage
        );
    }

    /** @test */
    public function it_should_show_the_exact_number_of_breaches(): void
    {
        $objectRuleError = Validator::make(['attr' => 'P@ssw0rd'], ['attr' => new Pwned(75)])->errors()->first();
        $stringRuleError = Validator::make(['attr' => 'P@ssw0rd'], ['attr' => 'pwned:75'])->errors()->first();

        $this->assertEquals($objectRuleError, $stringRuleError);
        $this->assertEquals(
            'attr was found in at least 49938 prior security incident(s). Please choose a more secure password.',
            $objectRuleError
        );
    }

    /** @test */
    public function it_should_pass_the_validation_when_a_network_error_occurs_during_lookup()
    {
        config([
            'logging.default' => 'test',
            'logging.channels' => [
                'test' => [
                    'driver' => 'custom',
                    'via' => function () {
                        $monolog = new Logger('test');
                        $monolog->pushHandler(new TestHandler());

                        return $monolog;
                    },
                ],
            ],
        ]);

        $this->assertTrue($this->passesValidator($pwnedPassword = 'password1'));
        $this->assertTrue($this->passesRule($pwnedPassword));

        tap(app('log')->getHandlers()[0]->getRecords(), function ($logMessages) use ($pwnedPassword) {
            $this->assertCount(2, $logMessages);

            tap($logMessages[0], function ($logMessage) use ($pwnedPassword) {
                $this->assertEquals('A password was marked as non-pwned due to issues during lookup.', $logMessage['message']);
                $this->assertTrue(password_verify($pwnedPassword, $logMessage['context']['hash']));
            });
        });
    }
}
