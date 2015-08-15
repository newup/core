<?php

namespace NewUp\Contracts\Templates;

interface SearchableStorageEngine
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
     * @return mixed
     */
    public function getInstalledPackages();

}