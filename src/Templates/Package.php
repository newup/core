<?php

namespace NewUp\Templates;

use NewUp\Exceptions\InvalidArgumentException;
use NewUp\Contracts\Packages\Package as PackageContract;
use NewUp\Contracts\Packages\PackageFactory;

class Package implements PackageContract, PackageFactory
{

    /**
     * The vendor name.
     *
     * @var string
     */
    protected $vendor = '';

    /**
     * The package name.
     *
     * @var string
     */
    protected $package = '';

    /**
     * The package description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The package license.
     *
     * @var string
     */
    protected $license = '';

    /**
     * The package authors.
     *
     * @var string
     */
    protected $authors = [];

    /**
     * Gets the vendor.
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Sets the vendor.
     *
     * @param $vendor
     * @return $this
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Gets the package name.
     *
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Sets the package name.
     *
     * @param $package
     * @return $this
     */
    public function setPackage($package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Sets the package description.
     *
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the package description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the package license.
     *
     * @param $license
     * @return $this
     */
    public function setLicense($license)
    {
        $this->license = $license;

        return $this;
    }

    /**
     * Gets the package license.
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Gets the package authors.
     *
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Gets the package authors as an array of arrays.
     *
     * @return array
     */
    protected function getAuthorsArray()
    {
        $authors = [];

        foreach ($this->authors as $author) {
            $authors[] = (array)$author;
        }

        return $authors;
    }

    /**
     * Sets the package authors.
     *
     * @param array $authors
     * @return $this
     */
    public function setAuthors(array $authors)
    {
        if (count($authors) == 0) { return; }

        foreach ($authors as $author) {
            $this->authors[] = (object)$author;
        }

        return $this;
    }

    /**
     * A helper to throw invalid argument exceptions when a value is null.
     *
     * @param $value
     * @param $message
     *
     * @throws \InvalidArgumentException
     */
    public static function throwInvalidArgumentException($value, $message)
    {
        if ($value == null) {
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Parses the package and vendor names.
     *
     * @param  $templateName
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function parseVendorAndPackage($templateName)
    {
        if ($templateName !== null) {
            $nameParts = explode('/', $templateName);

            if (count($nameParts) == 2) {
                if (strlen($nameParts[0]) > 0 && strlen($nameParts[1]) > 0) {
                    return $nameParts;
                }
            }
        }

        throw new InvalidArgumentException('The package name "' . $templateName .
            '" is invalid. Expected format "vendor/package".');
    }

    /**
     * Returns a new Package instance configured with user preferences.
     *
     * @return Package
     */
    public static function getConfiguredPackage()
    {
        $package = new Package;
        $package->setLicense(user_config('configuration.license'));
        $package->setAuthors(user_config('configuration.authors'));

        return $package;
    }

    /**
     * Returns a new package instance from the provided array.
     *
     * @param array $array
     * @param bool  $strict
     *
     * @return PackageContract
     * @throws \InvalidArgumentException
     */
    public static function fromArray(array $array, $strict = true)
    {
        $details = (object)$array;
        $packageNameDetails = self::parseVendorAndPackage(object_get($details, 'name', null));

        $description = object_get($details, 'description', null);
        $license = object_get($details, 'license', null);
        $authors = object_get($details, 'authors', null);

        // Throw exceptions if the developer wants us to be super strict.
        if ($strict) {
            self::throwInvalidArgumentException($description, 'Invalid package description.');
            self::throwInvalidArgumentException($license, 'Invalid package license.');
            self::throwInvalidArgumentException($authors, 'Invalid package authors.');
        }

        $package = new Package;
        $package->setAuthors($authors);
        $package->setDescription($description);
        $package->setLicense($license);
        $package->setVendor($packageNameDetails[0]);
        $package->setPackage($packageNameDetails[1]);

        return $package;
    }

    /**
     * Returns a new package instance from the provided file.
     *
     * The file must be valid JSON.
     *
     * @param string $path
     * @param bool   $strict
     * @return PackageContract
     */
    public static function fromFile($path, $strict = true)
    {
        if (file_exists($path)) {
            return self::fromArray(json_decode(file_get_contents($path), true), $strict);
        } else {
            return new Package;
        }
    }

    /**
     * Gets the name of the package in vendor/package format.
     *
     * @return string
     */
    public function getName()
    {
        return $this->vendor . '/' . $this->package;
    }

    /**
     * Converts the package details into JSON.
     *
     * @return string
     */
    public function toJson()
    {
        $packageDetails = new \stdClass;
        $packageDetails->name = $this->getName();
        $packageDetails->description = $this->getDescription();
        $packageDetails->license = $this->getLicense();
        $packageDetails->authors = $this->getAuthors();

        return json_encode($packageDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Converts the package details to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'license' => $this->getLicense(),
            'authors' => $this->getAuthorsArray()
        ];
    }

}