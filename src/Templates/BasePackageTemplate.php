<?php

namespace NewUp\Templates;

abstract class BasePackageTemplate
{

    /**
     * The paths that NewUp should ignore.
     *
     * @var array
     */
    protected $ignoredPaths = [];

    /**
     * THe paths that NewUp should automatically remove.
     *
     * @var array
     */
    protected $pathsToRemove = [];

    /**
     * A list of paths that NewUp should expand and process.
     *
     * @var array
     */
    protected $pathsToProcess = [];

    /**
     * A collection of CLI arguments the package can accept.
     *
     * @var array
     */
    protected $packageArguments = [];

    /**
     * A collection of CLI options the package can accept.
     *
     * @var array
     */
    protected $packageOptions = [];

    /**
     * Returns the paths that NewUp should ignore.
     *
     * @return array
     */
    public function getIgnoredPaths()
    {
        return $this->ignoredPaths;
    }

    /**
     * Returns the paths that NewUp should remove.
     *
     * @return array
     */
    public function getPathsToRemove()
    {
        return $this->ignoredPaths;
    }

    /**
     * Returns the paths that NewUp should process.
     *
     * @return array
     */
    public function getPathsToProcess()
    {
        return $this->pathsToProcess;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->packageOptions;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->packageArguments;
    }

}