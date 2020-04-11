<?php

namespace Ubient\PwnedPasswords\Api;

use Ubient\PwnedPasswords\Contracts\ApiGateway;

class FakeApiGateway implements ApiGateway
{
    /**
     * Indicates how frequently a password was found to be pwned.
     *
     * @param  string $password
     * @return int
     */
    public function search(string $password): int
    {
        if ($password === 'password1') {
            throw new \RuntimeException('Simulated network connectivity issue.');
        }

        return collect([
            'P@ssw0rd' => 49938,
            'hammertime6' => 5,
            'P@ssw0rd127' => 1,
        ])->get($password, 0);
    }
}
