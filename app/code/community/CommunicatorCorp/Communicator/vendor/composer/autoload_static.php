<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3264bc07e4bfe9b7ff8d98dd1426e770
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\PropertyAccess\\' => 33,
        ),
        'M' => 
        array (
            'MyCLabs\\Enum\\' => 13,
        ),
        'J' => 
        array (
            'JsonSchema\\' => 11,
        ),
        'I' => 
        array (
            'Icecave\\Isolator\\' => 17,
        ),
        'E' => 
        array (
            'Eloquent\\Enumeration\\' => 21,
            'Eloquent\\Composer\\Configuration\\' => 32,
        ),
        'C' => 
        array (
            'CommunicatorCorp\\Client\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\PropertyAccess\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/property-access',
        ),
        'MyCLabs\\Enum\\' => 
        array (
            0 => __DIR__ . '/..' . '/myclabs/php-enum/src',
        ),
        'JsonSchema\\' => 
        array (
            0 => __DIR__ . '/..' . '/justinrainbow/json-schema/src/JsonSchema',
        ),
        'Icecave\\Isolator\\' => 
        array (
            0 => __DIR__ . '/..' . '/icecave/isolator/src',
        ),
        'Eloquent\\Enumeration\\' => 
        array (
            0 => __DIR__ . '/..' . '/eloquent/enumeration/src',
        ),
        'Eloquent\\Composer\\Configuration\\' => 
        array (
            0 => __DIR__ . '/..' . '/eloquent/composer-config-reader/src',
        ),
        'CommunicatorCorp\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/communicatorcorp/client-php/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'MagentoHackathon\\Composer\\Magento' => 
            array (
                0 => __DIR__ . '/..' . '/magento-hackathon/magento-composer-installer/src',
            ),
        ),
        'E' => 
        array (
            'Eloquent\\Pops' => 
            array (
                0 => __DIR__ . '/..' . '/eloquent/pops/src',
            ),
            'Eloquent\\Liberator' => 
            array (
                0 => __DIR__ . '/..' . '/eloquent/liberator/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3264bc07e4bfe9b7ff8d98dd1426e770::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3264bc07e4bfe9b7ff8d98dd1426e770::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3264bc07e4bfe9b7ff8d98dd1426e770::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
