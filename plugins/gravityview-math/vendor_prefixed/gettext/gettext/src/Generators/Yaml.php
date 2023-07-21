<?php
/**
 * @license MIT
 *
 * Modified by GravityKit on 30-May-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMath\Foundation\ThirdParty\Gettext\Generators;

use GravityKit\GravityMath\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityMath\Foundation\ThirdParty\Gettext\Utils\MultidimensionalArrayTrait;
use Symfony\Component\Yaml\Yaml as YamlDumper;

class Yaml extends Generator implements GeneratorInterface
{
    use MultidimensionalArrayTrait;

    public static $options = [
        'includeHeaders' => false,
        'indent' => 2,
        'inline' => 4,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $options += static::$options;

        return YamlDumper::dump(
            static::toArray($translations, $options['includeHeaders']),
            $options['inline'],
            $options['indent']
        );
    }
}
