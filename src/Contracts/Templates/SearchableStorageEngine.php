<?php

namespace NewUp\Contracts\Templates;

interface SearchableStorageEngine extends  StorageEngine
{

    /**
     * Gets the vendors of all installed package templates.
     *
     * @return mixed
     */
    public function getInstalledVendors();

    /**
     * Gets all the installed packages.
     *
     * @param $includeProcessingPackages
     * @return mixed
     */
    public function getInstalledPackages($includeProcessingPackages = false);

}