<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 20-June-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityView\Foundation\ThirdParty\Gettext\Extractors;

use GravityKit\GravityView\Foundation\ThirdParty\Gettext\Translations;
use GravityKit\GravityView\Foundation\ThirdParty\Gettext\Utils\DictionaryTrait;

/**
 * Class to get gettext strings from plain json.
 */
class JsonDictionary extends Extractor implements ExtractorInterface
{
    use DictionaryTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $messages = json_decode($string, true);

        if (is_array($messages)) {
            static::fromArray($messages, $translations);
        }
    }
}
