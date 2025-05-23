<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaf1abc2896dd62030d12835252574087
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitaf1abc2896dd62030d12835252574087::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitaf1abc2896dd62030d12835252574087::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitaf1abc2896dd62030d12835252574087::$classMap;

        }, null, ClassLoader::class);
    }
}
