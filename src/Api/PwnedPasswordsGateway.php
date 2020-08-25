<?php

namespace Ubient\PwnedPasswords\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Ubient\PwnedPasswords\Contracts\ApiGateway;

class PwnedPasswordsGateway implements ApiGateway
{
    /**
     * Indicates how frequently a password was found to be pwned.
     *
     * @param  string $password
     * @return int
     */
    public function search(string $password): int
    {
        $hash = strtoupper(sha1($password));
        $hashPrefix = substr($hash, 0, 5);
        $hashSuffix = substr($hash, 5);

        /** @var Collection $hashes */
        $hashes = Cache::remember("Ubient\PwnedPasswords::$hashPrefix", 7200, function () use ($hashPrefix) {
            return $this->fetchHashes($hashPrefix);
        });

        return $hashes->get($hashSuffix, 0);
    }

    /**
     * Query the PwnedPasswords API for hashes starting with the given prefix.
     *
     * Returns a key-value Collection where the key is the hash-suffix
     * and the value is the amount of times the password was pwned.
     *
     * @param  string $hashPrefix
     * @return Collection
     * @throws RuntimeException|GuzzleException
     */
    protected function fetchHashes(string $hashPrefix): Collection
    {
        $response = (new GuzzleClient())->request('GET', 'https://api.pwnedpasswords.com/range/'.$hashPrefix);
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException("Couldn't query '$hashPrefix'");
        }

        $hashes = explode("\r\n", $response->getBody());

        return collect($hashes)
            ->mapWithKeys(function ($value) {
                [$hashSuffix, $occurrences] = explode(':', $value);

                return [$hashSuffix => $occurrences];
            });
    }
}
