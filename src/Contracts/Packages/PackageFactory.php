<?php

namespace NewUp\Contracts\Packages;

interface PackageFactory
{

    /**
     * Returns a new package instance from the provided an array.
     *
     * @param array $array
     * @return Package
     */
    public static function fromArray(array $array);

    /**
     * Returns a new package instance from the provided file.
     *
     * The file must be valid JSON.
     *
     * @param $path
     * @return Package
     */
    public static function fromFile($path);

}