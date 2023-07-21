<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-May-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMath\Foundation\ThirdParty\Illuminate\Contracts\Encryption;

interface Encrypter
{
    /**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @param  bool  $serialize
     * @return string
     */
    public function encrypt($value, $serialize = true);

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return string
     */
    public function decrypt($payload, $unserialize = true);
}
