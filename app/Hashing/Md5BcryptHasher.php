<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\AbstractHasher;

class Md5BcryptHasher extends AbstractHasher implements HasherContract
{
    /**
     * Hash the given value using Bcrypt (always upgrade to secure hashing for new passwords).
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     */
    public function make($value, array $options = [])
    {
        return password_hash($value, PASSWORD_BCRYPT, [
            'cost' => $options['rounds'] ?? 12,
        ]);
    }

    /**
     * Check the given plain value against a hash (Bcrypt or legacy MD5 fallback).
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        if (empty($hashedValue)) {
            return false;
        }

        // 1. If it's a standard Bcrypt hash, use standard password_verify
        if (str_starts_with($hashedValue, '$2y$')) {
            return password_verify($value, $hashedValue);
        }

        // 2. Fallback: If it's an MD5 hash (32 character hex string), check MD5
        if (strlen($hashedValue) === 32) {
            return md5($value) === $hashedValue;
        }

        return false;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        // MD5 hashes always need to be upgraded to Bcrypt!
        if (strlen($hashedValue) === 32) {
            return true;
        }

        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, [
            'cost' => $options['rounds'] ?? 12,
        ]);
    }
}
