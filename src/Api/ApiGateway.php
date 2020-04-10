<?php

namespace Ubient\PwnedPasswords\Api;

interface ApiGateway
{
    /**
     * Indicates how frequently a password was found to be pwned.
     *
     * @param  string $password
     * @return int
     */
    public function search(string $password): int;
}
