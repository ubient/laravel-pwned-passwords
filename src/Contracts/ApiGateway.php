<?php

namespace Ubient\PwnedPasswords\Contracts;

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
