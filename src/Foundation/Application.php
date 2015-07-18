<?php

namespace NewUp\Foundation;

use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{

    /**
     * The NewUp utility version.
     *
     * @var string
     */
    const VERSION = 'dev';

    public static $loader = null;

    public static function &getLoader()
    {
        if (self::$loader == null) {
            self::$loader = require base_path().'/vendor/autoload.php';
        }

        return self::$loader;
    }

}