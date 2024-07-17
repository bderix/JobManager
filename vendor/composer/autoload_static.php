<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit355741a9a6c3a9517978f278046048b7
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'JobManager\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'JobManager\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit355741a9a6c3a9517978f278046048b7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit355741a9a6c3a9517978f278046048b7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit355741a9a6c3a9517978f278046048b7::$classMap;

        }, null, ClassLoader::class);
    }
}