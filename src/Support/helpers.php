<?php

use Illuminate\Support\Str;
use NewUp\Foundation\Application;
use NewUp\Support\ANSIColor;
use NewUp\Contracts\Templates\StorageEngine;
use NewUp\Templates\Renderers\Collectors\InputCollector;


if (!function_exists('get_user_config_path')) {
    /**
     * Gets the user configuration directory.
     *
     * @return string
     */
    function get_user_config_path() {
        $userConfigurationPath = config_path('user');

        if (defined('NEWUP_CORE_USER_CONFIGURATION_DIRECTORY')) {
            $userConfigurationPath = NEWUP_CORE_USER_CONFIGURATION_DIRECTORY;
        }

        return $userConfigurationPath;
    }
}

if (!function_exists('user_config')) {
    /**
     * Get / set the specified user configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     * @return mixed
     */
    function user_config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config.user');
        }
        if (is_array($key)) {
            return app('config.user')->set($key);
        }
        return app('config.user')->get($key, $default);
    }
}

if (!function_exists('array_remove_value')) {
    /**
     * Removes the given value from the array.
     *
     * @param $array
     * @param $value
     */
    function array_remove_value(&$array, $value)
    {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }
    }
}

if (!function_exists('core_templates_path')) {
    /**
     * Gets the core templates path.
     *
     * @return string
     */
    function core_templates_path()
    {
        return storage_path() . '/templates/system/core/';
    }
}

if (!function_exists('template_storage_path')) {
    /**
     * Gets the template storage path.
     *
     * @return string
     */
    function template_storage_path()
    {
        return storage_path('templates/store/');
    }
}

if (!function_exists('load_system_template')) {
    /**
     * Get the contents of a system template by name.
     *
     * @param  $templateName
     * @return null|string
     */
    function load_system_template($templateName)
    {
        $templateFile = storage_path() . '/templates/system/' . $templateName . '.newup';

        if (file_exists($templateFile)) {
            return file_get_contents($templateFile);
        }

        return null;
    }
}

if (!function_exists('find_tse_template')) {
    /**
     * Locates a package location from storage based on package name.
     *
     * @param  $template
     * @return mixed
     */
    function find_tse_template($template)
    {
        return app(StorageEngine::class)->resolvePackagePath($template);
    }
}

if (!function_exists('load_core_template')) {
    /**
     * Get the contents of a core template by name.
     *
     * @param  $templateName
     * @return null|string
     */
    function load_core_template($templateName)
    {
        $templateFile = storage_path() . '/templates/system/core/' . $templateName;

        if (file_exists($templateFile)) {
            return file_get_contents($templateFile);
        }

        return null;
    }
}


if (!function_exists('package_vendor_namespace')) {
    /**
     * Returns a PHP namespace version of composer's package/vendor strings.
     *
     * @param  $vendor
     * @param  $package
     * @param  $forAutoload
     * @return string
     */
    function package_vendor_namespace($vendor, $package, $forAutoload = false)
    {
        $end = ($forAutoload ? '\\' : '');

        return Str::studly($vendor) . '\\' . Str::studly($package) . $end;
    }
}

if (!function_exists('get_composer_loader')) {
    /**
     * Gets the Composer loader.
     *
     * @return Composer\Autoload\ClassLoader
     */
    function &get_composer_loader()
    {
        return Application::getLoader();
    }
}

if (!function_exists('add_psr4')) {
    /**
     * Registers a PSR-4 Namespace and directory.
     *
     * @param $namespace
     * @param $directory
     */
    function add_psr4($namespace, $directory)
    {
        get_composer_loader()->setPsr4($namespace, $directory);
    }
}

if (!function_exists('scope_include')) {
    /**
     * Safely includes a file at runtime.
     *
     * Prevents access access to a class's
     * context from within the file.
     *
     * @param $include
     */
    function scope_include($include)
    {
        include $include;
    }
}

if (!function_exists('option')) {
    /**
     * Gets a command option by name.
     *
     * @param      $option
     * @param null $default
     *
     * @return mixed
     */
    function option($option, $default = null)
    {
        return array_get(app(InputCollector::class)->collect(), 'user_options.' . $option, $default);
    }
}

if (!function_exists('argument')) {
    /**
     * Gets a command argument by name.
     *
     * @param      $argument
     * @param null $default
     *
     * @return mixed
     */
    function argument($argument, $default = null)
    {
        return array_get(app(InputCollector::class)->collect(), 'user_options.' . $argument, $default);
    }
}

if (!function_exists('remove_ansi')) {
    /**
     * Removes ANSI escape sequences from strings.
     *
     * @param  $input
     * @return string
     */
    function remove_ansi($input)
    {
        $ansi = new ANSIColor;
        $input = str_replace('[[', chr(27) . '[', $input);
        return $ansi->colorStrip($input);
    }
}

if (!function_exists('normalize_line_endings')) {
    /**
     * Normalizes line endings.
     *
     * Adapted from
     * https://www.darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
     *
     * @param $string
     */
    function normalize_line_endings($string) {
        // Convert all line-endings to UNIX format
        $string = str_replace("\r\n", "\n", $string);
        $string = str_replace("\r", "\n", $string);
        // Don't allow out-of-control blank lines
        $string = preg_replace("/\n{2,}/", "\n" . "\n", $string);
        return $string;
    }
}