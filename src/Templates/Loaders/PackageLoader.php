<?php

namespace NewUp\Templates\Loaders;

use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Exceptions\InvalidPathException;
use NewUp\Exceptions\NewUpException;
use NewUp\Templates\BasePackageTemplate;
use NewUp\Templates\Package;

class PackageLoader
{

    /**
     * The Filesystem implementation.
     *
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $fileSystem)
    {
        $this->files = $fileSystem;
    }

    /**
     * Loads a package from a given directory.
     *
     * This function returns the string of the primary package class.
     *
     * @throws InvalidPathException Thrown when the package directory
     *                              does not exist.
     * @throws NewUpException
     * @param  $directory
     * @return string
     */
    public function loadPackage($directory)
    {
        if (!$this->files->exists($directory)) {
            throw new InvalidPathException("The directory {$directory} does not exist.");
        }

        if (!$this->files->exists($directory . '/composer.json')) {
            throw new InvalidPathException("There is no composer.json file in {$directory}");
        }

        if (!$this->files->exists($directory . '/_newup')) {
            throw new InvalidPathException("There is no _newup directory in {$directory}");
        }

        if (!$this->files->exists($directory . '/_newup/Package.php')) {
            throw new InvalidPathException("A Package.php file must be present in in \"_newup\" directory.");
        }

        $package = Package::fromFile($directory . '/composer.json',
            user_config('configuration.strictComposerValues', true));
        $namespace = package_vendor_namespace($package->getVendor(), $package->getPackage(), true);

        add_psr4($namespace, $directory . '/_newup');


        if (!class_exists($namespace . 'Package', true)) {
            throw new NewUpException("A valid reachable class named 'Package' must be defined.");
        }

        if (!is_subclass_of($namespace . 'Package', BasePackageTemplate::class)) {
            throw new NewUpException("The 'Package' class must extend ".BasePackageTemplate::class);
        }

        return $namespace . 'Package';
    }

}