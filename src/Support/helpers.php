<?php

use Illuminate\Support\Str;
use NewUp\Foundation\Application;

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

if (!function_exists('load_system_template')) {
    /**
     * Get the contents of a system template by name.
     *
     * @param $templateName
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

if (!function_exists('load_core_template')) {
    /**
     * Get the contents of a core template by name.
     *
     * @param $templateName
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
    function add_psr4($namespace, $directory) {
        get_composer_loader()->setPsr4($namespace, $directory);
    }
}

if (!function_exists('scope_include')) {
    function scope_include($include) {
        include $include;
    }
}