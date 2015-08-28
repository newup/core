<?php

use Illuminate\Support\Str;
use NewUp\Foundation\Application;
use NewUp\Support\ANSIColor;
use NewUp\Contracts\Templates\StorageEngine;
use NewUp\Templates\Renderers\Collectors\InputCollector;

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
    function template_storage_path() {
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
    function find_tse_template($template) {
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
    function add_psr4($namespace, $directory) {
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
    function scope_include($include) {
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
    function option($option, $default = null) {
        return array_get(app(InputCollector::class)->collect(), 'user_options.'.$option, $default);
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
    function argument($argument, $default = null) {
        return array_get(app(InputCollector::class)->collect(), 'user_options.'.$argument, $default);
    }
}

if (!function_exists('remove_ansi')) {
    /**
     * Removes ANSI escape sequences from strings.
     *
     * @param  $input
     * @return string
     */
    function remove_ansi($input) {
        $ansi = new ANSIColor;
        $input = str_replace('[[', chr(27).'[', $input);
        return $ansi->colorStrip($input);
    }
}