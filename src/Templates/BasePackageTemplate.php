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
     * A collection of patterns to simply copy.
     *
     * @var array
     */
    protected $copyVerbatim = [];

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
        return $this->pathsToRemove;
    }

    /**
     * Gets the patterns that NewUp should simply copy.
     *
     * @return array
     */
    public function getVerbatimPatterns()
    {
        return $this->copyVerbatim;
    }

    /**
     * Returns the paths that NewUp should process.
     *
     * @return array
     */
    public function getPathsToProcess()
    {
        return [

        ];
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