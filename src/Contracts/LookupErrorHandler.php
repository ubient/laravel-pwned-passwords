<?php

namespace Ubient\PwnedPasswords\Contracts;

use Throwable;

interface LookupErrorHandler
{
    /**
     * Handles errors that occur during the lookup of a potentially Pwned Password.
     * Returns a boolean indicating whether the unverified password is accepted.
     *
     * @param  Throwable  $exception
     * @param  string  $password
     * @return bool
     */
    public function handle(Throwable $exception, string $password): bool;
}
