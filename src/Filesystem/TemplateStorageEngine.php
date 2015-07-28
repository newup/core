<?php

namespace NewUp\Filesystem;

use Illuminate\Support\Str;
use NewUp\Contracts\Filesystem\Filesystem as FileSystemContract;
use NewUp\Contracts\Templates\StorageEngine;
use NewUp\Exceptions\InvalidArgumentException;
use NewUp\Templates\Generators\PathNormalizer;

class TemplateStorageEngine implements StorageEngine
{

    use PathNormalizer;

    /**
     * The Filesystem implementation.
     *
     * @var FileSystemContract
     */
    protected $files = null;

    /**
     * The template storage engine path.
     *
     * @var string
     */
    protected $templateStoragePath = '';

    public function __construct(FileSystemContract $filesystem, $templateStoragePath)
    {
        $this->files = $filesystem;
        $this->templateStoragePath = $this->normalizePath($templateStoragePath);
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
        $packagePath = $this->resolvePackagePath($packageName);

        if (!$this->files->exists($packagePath)) {
            $this->files->makeDirectory($packagePath);
        }
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
        $this->files->delete($this->resolvePackagePath($packageName));
    }

    /**
     * Gets the package version number from a package string, if present.
     *
     * @param  $packageName
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function getPackageVersion($packageName)
    {
        $versionParts = explode(':', $packageName);

        if (count($versionParts) == 2) {
            return $versionParts[1];
        } else if (count($versionParts) > 2) {
            throw new InvalidArgumentException("Supplied package name is invalid: {$packageName}.");
        }

        return null;
    }

    /**
     * Gets the package name without any version string.
     *
     * @param $packageName
     *
     * @return mixed
     * @throws \NewUp\Exceptions\InvalidArgumentException
     */
    private function getPackageWithoutVersionString($packageName)
    {
        $packageParts = explode(':', $packageName);

        if (strlen($packageName) > 0 && !Str::contains($packageName, ':')) {
            return $packageName;
        }

        if (count($packageParts) > 0) {
            if (strlen($packageParts[0]) > 0) {
                return $packageParts[0];
            }
        }

        throw new InvalidArgumentException("Supplied package name is invalid: {$packageName}.");
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
        $packageVersion = $this->getPackageVersion($packageName);
        $packageName    = $this->getPackageWithoutVersionString($packageName);

        $packagePath = template_storage_path().$packageName.'/';

        if ($packageVersion !== null) {
            $packagePath .= $packageVersion.'/';
        }

        return $this->normalizePath($packagePath);
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

    /**
     * Gets the storage path.
     *
     * @return string
     */
    public function getStoragePath()
    {
        return $this->templateStoragePath;
    }


}