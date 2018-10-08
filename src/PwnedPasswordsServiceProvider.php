<?php

namespace Ubient\PwnedPasswords;

use Ubient\PwnedPasswords\Rules\Pwned;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Ubient\PwnedPasswords\Api\ApiGateway;
use Ubient\PwnedPasswords\Api\PwnedPasswordsGateway;

class PwnedPasswordsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('pwned', Pwned::class, Pwned::VALIDATION_ERROR_MESSAGE);
        Validator::replacer('pwned', function($message, $attribute, $rule, $parameters) {
            return str_replace(':num', array_shift($parameters) ?? 1, $message);
        });
    }

    public function register()
    {
        $this->app->bind(ApiGateway::class, PwnedPasswordsGateway::class);
    }
}
