<?php namespace NewUp\Contracts\Packages;

interface Package
{

    /**
     * Gets the vendor.
     *
     * @return string
     */
    public function getVendor();

    /**
     * Sets the vendor.
     *
     * @param $vendor
     */
    public function setVendor($vendor);


    /**
     * Gets the package name.
     *
     * @return string
     */
    public function getPackage();

    /**
     * Sets the package name.
     *
     * @param $package
     */
    public function setPackage($package);

    /**
     * Sets the package description.
     *
     * @param $description
     */
    public function setDescription($description);

    /**
     * Gets the package description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the package license.
     *
     * @param $license
     */
    public function setLicense($license);

    /**
     * Gets the package license.
     *
     * @return string
     */
    public function getLicense();

    /**
     * Gets the package authors.
     *
     * @return array
     */
    public function getAuthors();

    /**
     * Sets the package authors.
     *
     * @param array $authors
     */
    public function setAuthors(array $authors);

    /**
     * Gets the name of the package in vendor/package format.
     *
     * @return string
     */
    public function getName();

    /**
     * Converts the package details into JSON.
     *
     * @return string
     */
    public function toJson();

    /**
     * Converts the package details to an array.
     *
     * @return array
     */
    public function toArray();

}