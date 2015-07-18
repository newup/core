<?php

namespace NewUp\Templates;

use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Exceptions\InvalidPathException;

class TemplateInitializer
{

    /**
     * The Filesystem implementation.
     *
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Initializes a package template in the provided directory.
     *
     * @throws InvalidPathException
     * @param $vendor
     * @param $package
     * @param $directory
     */
    public function initialize($vendor, $package, $directory)
    {
        if (!$this->files->exists($directory) || !$this->files->isDirectory($directory)) {
            throw new InvalidPathException("{$directory} does not exist or is not a valid directory.");
        }

        $package = new Package;
        $package->setVendor($vendor);
        $package->setPackage($package);
        $package->setDescription('Give your package template a good description');
        $package->setLicense(config('user.configuration.license', ''));
        $package->setAuthors(config('user.configuration.authors', []));


    }


}