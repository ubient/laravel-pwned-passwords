<?php

namespace Ubient\PwnedPasswords\Rules;

use Illuminate\Contracts\Validation\Rule;
use Throwable;
use Ubient\PwnedPasswords\Contracts\ApiGateway;
use Ubient\PwnedPasswords\Contracts\LookupErrorHandler;

class Pwned implements Rule
{
    /**
     * @var ApiGateway
     */
    protected $gateway;

    /**
     * @var int
     */
    protected $threshold;

    /**
     * @var int
     */
    protected $pwned_count = 1;

    /**
     * Create a new rule instance.
     *
     * @param  int  $threshold
     * @return void
     */
    public function __construct(int $threshold = 1)
    {
        $this->gateway = app(ApiGateway::class);
        $this->threshold = $threshold;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $this->pwned_count = $this->gateway->search($value);
            return $this->pwned_count < $this->threshold;
        } catch (Throwable $exception) {
            return app(LookupErrorHandler::class)->handle($exception, $value);
        }
    }

    /**
     * Determine if the extended validation rule passes.
     *
     * @see https://laravel.com/docs/5.7/validation#using-extensions
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     *
     * @deprecated
     */
    public function validate($attribute, $value, $parameters)
    {
        $this->threshold = (int) (array_shift($parameters) ?? 1);

        return $this->passes($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('PwnedPasswords::validation.error_message', ['num' => $this->pwned_count]);
    }
}
