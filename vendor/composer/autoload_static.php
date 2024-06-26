<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1c58df52648e69cf7d4c8a0c18acde19
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit1c58df52648e69cf7d4c8a0c18acde19::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1c58df52648e69cf7d4c8a0c18acde19::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1c58df52648e69cf7d4c8a0c18acde19::$classMap;

        }, null, ClassLoader::class);
    }
}
