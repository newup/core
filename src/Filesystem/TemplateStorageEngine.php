<?php

namespace NewUp\Filesystem;

use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Contracts\Templates\StorageEngine;

class TemplateStorageEngine implements StorageEngine
{

    /**
     * The Filesystem implementation.
     *
     * @var Filesystem
     */
    protected $files = null;

    /**
     * The template storage engine path.
     *
     * @var string
     */
    protected $templateStoragePath = '';

    public function __construct(Filesystem $filesystem, $templateStoragePath)
    {
        $this->files = $filesystem;
        $this->templateStoragePath = $templateStoragePath;
    }

    /**
     * Adds a package to the storage engine.
     *
     * @param $packageName
     *
     * @return mixed
     */
    public function addPackage($packageName)
    {
        // TODO: Implement addPackage() method.
    }

    /**
     * Removes a package from the storage engine.
     *
     * @param $packageName
     *
     * @return mixed
     */
    public function removePackage($packageName)
    {
        // TODO: Implement removePackage() method.
    }

    /**
     * Gets the package version number from a package string, if present.
     *
     * @param $packageName
     *
     * @return mixed
     */
    public function getPackageVersion($packageName)
    {
        // TODO: Implement getPackageVersion() method.
    }

    /**
     * Gets the path to a stored package.
     *
     * @param $packageName
     *
     * @return mixed
     */
    public function resolvePackagePath($packageName)
    {
        // TODO: Implement resolvePackagePath() method.
    }

    /**
     * Updates a given package.
     *
     * @param $packageName
     *
     * @return mixed
     */
    public function updatePackage($packageName)
    {
        // TODO: Implement updatePackage() method.
    }

    /**
     * Configures a given package by name.
     *
     * @param $packageName
     *
     * @return mixed
     */
    public function configurePackage($packageName)
    {
        // TODO: Implement configurePackage() method.
    }

    /**
     * Determines if a package exists by name.
     *
     * @param $packageName
     *
     * @return mixed
     */
    public function packageExists($packageName)
    {
        // TODO: Implement packageExists() method.
    }

    /**
     * Gets the packages currently installed.
     *
     * @return mixed
     */
    public function getPackages()
    {
        // TODO: Implement getPackages() method.
    }

    /**
     * Gets the packages currently installed that match the given pattern.
     *
     * @param $pattern
     *
     * @return mixed
     */
    public function getPackagesLike($pattern)
    {
        // TODO: Implement getPackagesLike() method.
    }


}