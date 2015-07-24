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
     * A collection of the parsed options.
     *
     * @var array
     */
    protected $parsedOptions = [];

    /**
     * A collection of the parsed arguments.
     *
     * @var array
     */
    protected $parsedArguments = [];

    /**
     * Get the value of a command option.
     *
     * @param      $name
     * @param null $default
     * @return mixed
     */
    public function option($name, $default = null)
    {
        return array_get($this->parsedOptions, $name, $default);
    }

    /**
     * Get the value of a command argument.
     *
     * @param      $name
     * @param null $default
     * @return mixed
     */
    public function argument($name, $default = null)
    {
        return array_get($this->parsedArguments, $name, $default);
    }

    /**
     * Sets the parsed options.
     *
     * @param $options
     */
    public function setParsedOptions($options)
    {
        $this->parsedOptions = $options;
    }

    /**
     * Sets the parsed arguments.
     *
     * @param $arguments
     */
    public function setParsedArguments($arguments)
    {
        $this->parsedArguments = $arguments;
    }

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
    public static function getOptions()
    {
        return [

        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public static function getArguments()
    {
        return [

        ];
    }

}