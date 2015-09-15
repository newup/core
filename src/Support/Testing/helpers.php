<?php

if (!function_exists('getFixturePath')) {
    /**
     * Returns the full path of a fixture file.
     *
     * @param $file
     * @return string
     */
    function getFixturePath($file) {
        return realpath(__DIR__.'/../../../tests/fixtures/'.$file);
    }
}

if (!function_exists('loadFixtureContent')) {
    /**
     * Returns the content of a given fixture file.
     *
     * @param $file
     * @return string
     */
    function loadFixtureContent($file) {
        return normalize_line_endings(file_get_contents(getFixturePath($file)));
    }
}

if (PHP_MAJOR_VERSION < 7) {
    if (!class_exists('TypeError')) {
        class TypeError { }
    }
}