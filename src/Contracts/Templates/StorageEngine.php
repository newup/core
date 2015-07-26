<?php

namespace NewUp\Contracts\Templates;

interface StorageEngine {

    /**
     * Adds a package to the storage engine.
     *
     * @param $packageName
     * @return mixed
     */
    public function addPackage($packageName);

    /**
     * Removes a package from the storage engine.
     *
     * @param $packageName
     * @return mixed
     */
    public function removePackage($packageName);

    /**
     * Gets the package version number from a package string, if present.
     *
     * @param $packageName
     * @return mixed
     */
    public function getPackageVersion($packageName);

    /**
     * Gets the path to a stored package.
     *
     * @param $packageName
     * @return mixed
     */
    public function resolvePackagePath($packageName);

    /**
     * Updates a given package.
     *
     * @param $packageName
     * @return mixed
     */
    public function updatePackage($packageName);

    /**
     * Configures a given package by name.
     *
     * @param $packageName
     * @return mixed
     */
    public function configurePackage($packageName);

    /**
     * Determines if a package exists by name.
     *
     * @param $packageName
     * @return mixed
     */
    public function packageExists($packageName);

    /**
     * Gets the packages currently installed.
     *
     * @return mixed
     */
    public function getPackages();

    /**
     * Gets the packages currently installed that match the given pattern.
     *
     * @param $pattern
     * @return mixed
     */
    public function getPackagesLike($pattern);

    /**
     * Gets the storage path.
     *
     * @return string
     */
    public function getStoragePath();

}