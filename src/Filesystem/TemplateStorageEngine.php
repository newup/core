<?php

namespace NewUp\Filesystem;

use Illuminate\Support\Str;
use NewUp\Contracts\Filesystem\Filesystem as FileSystemContract;
use NewUp\Contracts\Templates\StorageEngine;
use NewUp\Exceptions\InvalidArgumentException;
use NewUp\Foundation\Composer\Composer;
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
     * The Composer instance.
     *
     * @var \NewUp\Foundation\Composer\Composer
     */
    protected $composer;

    /**
     * The template storage engine path.
     *
     * @var string
     */
    protected $templateStoragePath = '';

    public function __construct(FileSystemContract $filesystem, Composer $composer, $templateStoragePath)
    {
        $this->files               = $filesystem;
        $this->templateStoragePath = $this->normalizePath($templateStoragePath);
        $this->composer            = $composer;
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
        $this->writePackageInstallationInstructions($packagePath, $packageName);
        $this->composer->setWorkingPath($packagePath);
        $this->composer->installPackage($this->getCleanPackageNameString($packageName),
            $this->preparePackageOptions($packageName));

    }

    private function writePackageInstallationInstructions($path, $packageName)
    {
        $this->files->put($path . DIRECTORY_SEPARATOR . '_newup_install_instructions', $packageName);
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
        $this->files->deleteDirectory($this->resolvePackagePath($packageName), false);
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
            return explode('>', $versionParts[1])[0];
        } else if (count($versionParts) > 2) {
            throw new InvalidArgumentException("Supplied package name is invalid: {$packageName}.");
        }

        return null;
    }

    /**
     * Returns an array of options that is compatible with the
     * Composer class.
     *
     * @param $packageString
     * @return array
     */
    private function preparePackageOptions($packageString)
    {
        $packageOptions = explode('>', $packageString);
        array_shift($packageOptions);

        if (count($packageOptions) == 0) {
            return [];
        }

        $packageOptions = explode(',', $packageOptions[0]);

        $processedOptions = [];

        foreach ($packageOptions as $option) {
            $tempOption = explode('=', $option);

            if (count($tempOption) == 1) {
                $processedOptions[$tempOption[0]] = null;
            } elseif (count($tempOption) == 2) {
                $processedOptions[$tempOption[0]] = $tempOption[1];
            }

        }

        return $processedOptions;
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
     * @param $packageName
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function getCleanPackageNameString($packageName)
    {
        return explode('>', $this->getPackageWithoutVersionString($packageName))[0];
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
        $packageName    = $this->getCleanPackageNameString($packageName);
        $packagePath    = template_storage_path() . $packageName;

        if ($packageVersion !== null) {
            $packagePath .= '_{' . $packageVersion . '}' . DIRECTORY_SEPARATOR;
        }

        return $this->normalizePath($packagePath, true);
    }

    /**
     * Updates a given package.
     *
     * This is the wrong method if you are looking
     * for something like 'composer update'. Look
     * at the 'configurePackage' method instead.
     *
     * @param $packageName
     * @return mixed
     */
    public function updatePackage($packageName)
    {
        $this->removePackage($packageName);
        $this->addPackage($packageName);
    }

    /**
     * Configures a given package by name.
     *
     * Use this when you want to run something
     * like 'composer update'.
     *
     * @param $packageName
     * @return mixed
     */
    public function configurePackage($packageName)
    {
        $packagePath = $this->resolvePackagePath($packageName);

        $this->composer->setWorkingPath($packagePath);
        $this->composer->updatePackageDependencies($this->preparePackageOptions($packageName));
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
        $packagePath = $this->resolvePackagePath($packageName);

        return $this->files->exists($packagePath . DIRECTORY_SEPARATOR . 'composer.json');
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