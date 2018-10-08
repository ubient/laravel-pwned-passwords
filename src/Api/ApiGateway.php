<?php

namespace Ubient\PwnedPasswords\Api;

interface ApiGateway
{
    /**
     * Indicates how frequently a password was found to be pwned.
     *
     * @param  string $password
     * @throws \RuntimeException
     * @return int
     */
    public function search(string $password): int;
}
