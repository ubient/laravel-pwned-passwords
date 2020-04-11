<?php

namespace Ubient\PwnedPasswords;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Ubient\PwnedPasswords\Api\PwnedPasswordsGateway;
use Ubient\PwnedPasswords\Contracts\ApiGateway;
use Ubient\PwnedPasswords\Contracts\LookupErrorHandler;
use Ubient\PwnedPasswords\Handler\LogHandler;
use Ubient\PwnedPasswords\Rules\Pwned;

class PwnedPasswordsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('pwned', Pwned::class, Pwned::VALIDATION_ERROR_MESSAGE);
        Validator::replacer('pwned', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':num', array_shift($parameters) ?? 1, $message);
        });
    }

    public function register()
    {
        $this->app->bind(ApiGateway::class, PwnedPasswordsGateway::class);
        $this->app->bind(LookupErrorHandler::class, LogHandler::class);
    }
}
