<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-May-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMath\Foundation\ThirdParty\Illuminate\Contracts\Support;

interface Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);
}
