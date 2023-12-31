<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMaps\Geocoder\Provider;

interface LocaleAwareProviderInterface extends ProviderInterface
{
    /**
     * Return the locale to be used in locale aware requests.
     *
     * In case there is no locale in use, null is returned.
     *
     * @return string|null
     */
    public function getLocale();
}
