<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3f8c3eff9a3d436e5020dac34e148264
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'XLSXWriter' => __DIR__ . '/..' . '/mk-j/php_xlsxwriter/xlsxwriter.class.php',
        'XLSXWriter_BuffererWriter' => __DIR__ . '/..' . '/mk-j/php_xlsxwriter/xlsxwriter.class.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit3f8c3eff9a3d436e5020dac34e148264::$classMap;

        }, null, ClassLoader::class);
    }
}
