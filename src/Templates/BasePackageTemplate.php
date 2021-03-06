<?php

namespace NewUp\Templates;

use NewUp\Console\Commandable;
use NewUp\Contracts\Templates\Renderer;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BasePackageTemplate
{

    use Commandable;

    /**
     * The Renderer implementation.
     *
     * @var \NewUp\Contracts\Templates\Renderer
     */
    protected $templateRenderer = null;

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
     * A collection of files and patterns to process anyway.
     *
     * The $copyVerbatim collection allows template authors
     * to simply copy files to the output directory based
     * on a given pattern. However, sometimes it might
     * be necessary to process a given file that
     * could be matched by one of the patterns.
     * Add those files/patterns to this list
     * to have them processed anyways.
     *
     * @var array
     */
    protected $copyVerbatimExclude = [];

    /**
     * Determines if the Builder command should display default interactions.
     *
     * @var bool
     */
    protected $isQuiet = false;

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
     * Gets the patterns that NewUp should process anyways.
     *
     * @return array
     */
    public function getVerbatimExcludePatterns()
    {
        return $this->copyVerbatimExclude;
    }

    /**
     * Returns the paths that NewUp should transform.
     *
     * @return array
     */
    public function getTransformPaths()
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

    /**
     * Called when the builder has loaded the package class.
     *
     * @return mixed
     */
    public function builderLoaded()
    {

    }

    /**
     * Sets the Renderer instance.
     *
     * @param \NewUp\Contracts\Templates\Renderer $renderer
     */
    public function setRendererInstance(Renderer $renderer)
    {
        $this->templateRenderer = $renderer;
    }

    /**
     * Shares data with package template files.
     *
     * @param       $key   The name of the variable to share.
     * @param  null $value The value of the variable to share.
     * @return $this
     */
    public function shareData($key, $value= null)
    {
        if (is_array($key)) {
            foreach  ($key as $variableName => $variableValue) {
                $this->templateRenderer->setData($variableName, $variableValue);
            }
            return $this;
        }

        $this->templateRenderer->setData($key, $value);
        return $this;
    }

    /**
     * Shares data with package template files.
     *
     * @param       $key   The name of the variable to share.
     * @param  null $value The value of the variable to share.
     * @return $this
     */
    public function with($key, $value = null)
    {
        return $this->shareData($key, $value);
    }

    /**
     * Adds a path to the ignore list.
     *
     * @param $path
     * @return $this
     */
    public function ignorePath($path)
    {
        if (is_array($path)) {
            foreach ($path as $pathToIgnore) {
                $this->ignorePath($pathToIgnore);
            }

            return $this;
        }

        $this->ignoredPaths[] = $path;
        return $this;
    }

    /**
     * Gets the output directory the template is being written to.
     *
     * @return string
     */
    protected function outputDirectory()
    {
        return $this->argument('newup-output-directory');
    }

    /**
     * Gets whether or not the builder command should be quiet.
     *
     * @return bool
     */
    public function isQuiet()
    {
        return $this->isQuiet;
    }

}