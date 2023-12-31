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

use GravityKit\GravityMaps\Geocoder\Exception\NoResultException;
use GravityKit\GravityMaps\Geocoder\Exception\InvalidCredentialsException;
use GravityKit\GravityMaps\Geocoder\Exception\UnsupportedException;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
interface ProviderInterface
{
    /**
     * Returns an associative array with data treated by the provider.
     *
     * @param string $address An address (IP or street).
     *
     * @throws NoResultException           If the address could not be resolved
     * @throws InvalidCredentialsException If the credentials are invalid
     * @throws UnsupportedException        If IPv4, IPv6 or street is not supported
     *
     * @return array
     */
    public function getGeocodedData($address);

    /**
     * Returns an associative array with data treated by the provider.
     *
     * @param array $coordinates Coordinates (latitude, longitude).
     *
     * @throws NoResultException           If the coordinates could not be resolved
     * @throws InvalidCredentialsException If the credentials are invalid
     * @throws UnsupportedException        If reverse geocoding is not supported
     *
     * @return array
     */
    public function getReversedData(array $coordinates);

    /**
     * Returns the provider's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the maximum number of returned results.
     *
     * @param integer $maxResults
     *
     * @return ProviderInterface
     */
    public function setMaxResults($maxResults);
}
