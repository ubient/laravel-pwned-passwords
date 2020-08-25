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
        $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang/vendor/pwned-passwords')], 'lang');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'PwnedPasswords');

        Validator::extend('pwned', Pwned::class);
        Validator::replacer('pwned', function ($message, $attribute, $rule, $parameters) {
            return trans('PwnedPasswords::validation.pwned', [
                'attribute' => $attribute,
                'num' => array_shift($parameters) ?? 1,
            ]);
        });
    }

    public function register()
    {
        $this->app->bind(ApiGateway::class, PwnedPasswordsGateway::class);
        $this->app->bind(LookupErrorHandler::class, LogHandler::class);
    }
}
