<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-May-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use GravityKit\GravityMath\Symfony\Component\HttpFoundation\Cookie;

$r = require __DIR__.'/common.inc';

$r->headers->setCookie(new Cookie('CookieSamesiteStrictTest', 'StrictValue', 0, '/', null, false, true, false, Cookie::SAMESITE_STRICT));
$r->sendHeaders();
