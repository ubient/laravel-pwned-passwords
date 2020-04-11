<?php

namespace Ubient\PwnedPasswords\Handler;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;
use Ubient\PwnedPasswords\Contracts\LookupErrorHandler;

class LogHandler implements LookupErrorHandler
{
    /**
     * Handles errors that occur during the lookup of a potentially Pwned Password.
     * Returns a boolean indicating whether the unverified password is accepted.
     *
     * @param  Throwable  $exception
     * @param  string  $password
     * @return bool
     */
    public function handle(Throwable $exception, string $password): bool
    {
        Log::warning('A password was marked as non-pwned due to issues during lookup.', [
            'hash' => Hash::make($password),
        ]);

        return true;
    }
}
