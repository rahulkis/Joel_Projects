<?php

/**
 * Simple autoloader that follow the PHP Standards Recommendation #0 (PSR-0)
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md for more informations.
 *
 * Code inspired from the SplClassLoader RFC
 * @see https://wiki.php.net/rfc/splclassloader#example_implementation
 *
 * @license MIT
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    if (0 != strpos($className, 'GravityKit\GravityMaps\Geocoder')) {
        return false;
    }
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName = __DIR__ . DIRECTORY_SEPARATOR . $fileName . $className . '.php';
    if (is_file($fileName)) {
        require $fileName;

        return true;
    }

    return false;
});
