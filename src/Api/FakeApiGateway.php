<?php

namespace Ubient\PwnedPasswords\Api;

class FakeApiGateway implements ApiGateway
{
    /**
     * Indicates how frequently a password was found to be pwned.
     *
     * @param  string $password
     * @throws \RuntimeException
     * @return int
     */
    public function search(string $password): int
    {
        return collect([
            'P@ssw0rd' => 49938,
            'hammertime6' => 5,
            'P@ssw0rd127' => 1,
        ])->get($password, 0);
    }
}
