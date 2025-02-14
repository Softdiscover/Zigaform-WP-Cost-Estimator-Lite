<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteab8eff448d56ba652cf97d3eb867c63
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zigaform\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zigaform\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Zigaform\\Admin\\List_data' => __DIR__ . '/../..' . '/includes/admin/class-admin-list.php',
        'Zigaform\\Template' => __DIR__ . '/../..' . '/includes/class-template.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticIniteab8eff448d56ba652cf97d3eb867c63::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticIniteab8eff448d56ba652cf97d3eb867c63::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticIniteab8eff448d56ba652cf97d3eb867c63::$classMap;

        }, null, ClassLoader::class);
    }
}
