<?php

namespace NewUp\Contracts\Packages;

interface PackageFactory
{

    /**
     * Returns a new package instance from the provided an array.
     *
     * @param array $array
     * @param bool  $strict
     * @return Package
     */
    public static function fromArray(array $array, $strict = true);

    /**
     * Returns a new package instance from the provided file.
     *
     * The file must be valid JSON.
     *
     * @param string $path
     * @param bool   $strict
     * @return Package
     */
    public static function fromFile($path, $strict = true);

}