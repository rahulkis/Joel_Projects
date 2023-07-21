<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

if (!defined('ABSPATH')) exit;


class ComposerStaticInit58b5b1f652a9036c7a4ef3b76b5b5ee6
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MailPoet\\Premium\\' => 17,
            'MailPoetGenerated\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MailPoet\\Premium\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
        'MailPoetGenerated\\' => 
        array (
            0 => __DIR__ . '/../..' . '/generated',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'MailPoetGenerated\\PremiumCachedContainer' => __DIR__ . '/../..' . '/generated/PremiumCachedContainer.php',
        'MailPoet\\Premium\\API\\JSON\\v1\\Bounces' => __DIR__ . '/../..' . '/lib/API/JSON/v1/Bounces.php',
        'MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\StatsResponseBuilder' => __DIR__ . '/../..' . '/lib/API/JSON/v1/ResponseBuilders/StatsResponseBuilder.php',
        'MailPoet\\Premium\\API\\JSON\\v1\\ResponseBuilders\\SubscriberDetailedStatsResponseBuilder' => __DIR__ . '/../..' . '/lib/API/JSON/v1/ResponseBuilders/SubscriberDetailedStatsResponseBuilder.php',
        'MailPoet\\Premium\\API\\JSON\\v1\\Stats' => __DIR__ . '/../..' . '/lib/API/JSON/v1/Stats.php',
        'MailPoet\\Premium\\API\\JSON\\v1\\SubscriberDetailedStats' => __DIR__ . '/../..' . '/lib/API/JSON/v1/SubscriberDetailedStats.php',
        'MailPoet\\Premium\\Config\\Env' => __DIR__ . '/../..' . '/lib/Config/Env.php',
        'MailPoet\\Premium\\Config\\Hooks' => __DIR__ . '/../..' . '/lib/Config/Hooks.php',
        'MailPoet\\Premium\\Config\\Initializer' => __DIR__ . '/../..' . '/lib/Config/Initializer.php',
        'MailPoet\\Premium\\Config\\Localizer' => __DIR__ . '/../..' . '/lib/Config/Localizer.php',
        'MailPoet\\Premium\\Config\\Renderer' => __DIR__ . '/../..' . '/lib/Config/Renderer.php',
        'MailPoet\\Premium\\DI\\ContainerConfigurator' => __DIR__ . '/../..' . '/lib/DI/ContainerConfigurator.php',
        'MailPoet\\Premium\\Newsletter\\StatisticsClicksRepository' => __DIR__ . '/../..' . '/lib/Newsletter/StatisticsClicksRepository.php',
        'MailPoet\\Premium\\Newsletter\\StatisticsOpensRepository' => __DIR__ . '/../..' . '/lib/Newsletter/StatisticsOpensRepository.php',
        'MailPoet\\Premium\\Newsletter\\StatisticsUnsubscribesRepository' => __DIR__ . '/../..' . '/lib/Newsletter/StatisticsUnsubscribesRepository.php',
        'MailPoet\\Premium\\Newsletter\\Stats\\Bounces' => __DIR__ . '/../..' . '/lib/Newsletter/Stats/Bounces.php',
        'MailPoet\\Premium\\Newsletter\\Stats\\PurchasedProducts' => __DIR__ . '/../..' . '/lib/Newsletter/Stats/PurchasedProducts.php',
        'MailPoet\\Premium\\Newsletter\\Stats\\SubscriberEngagement' => __DIR__ . '/../..' . '/lib/Newsletter/Stats/SubscriberEngagement.php',
        'MailPoet\\Premium\\Segments\\DynamicSegments\\SegmentCombinations' => __DIR__ . '/../..' . '/lib/Segments/DynamicSegments/SegmentCombinations.php',
        'MailPoet\\Premium\\Subscriber\\Stats\\SubscriberNewsletterStats' => __DIR__ . '/../..' . '/lib/Subscriber/Stats/SubscriberNewsletterStats.php',
        'MailPoet\\Premium\\Subscriber\\Stats\\SubscriberNewsletterStatsRepository' => __DIR__ . '/../..' . '/lib/Subscriber/Stats/SubscriberNewsletterStatsRepository.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit58b5b1f652a9036c7a4ef3b76b5b5ee6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit58b5b1f652a9036c7a4ef3b76b5b5ee6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit58b5b1f652a9036c7a4ef3b76b5b5ee6::$classMap;

        }, null, ClassLoader::class);
    }
}
