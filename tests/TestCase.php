<?php

namespace Ubient\PwnedPasswords\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Ubient\PwnedPasswords\PwnedPasswordsServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PwnedPasswordsServiceProvider::class,
        ];
    }
}
