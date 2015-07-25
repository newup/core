<?php

namespace NewUp\Templates\Renderers\Collectors;

use NewUp\Contracts\DataCollector;

class InputCollector implements DataCollector
{

    /**
     * The user supplied options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The user supplied arguments.
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Sets the user supplied arguments.
     *
     * @param $args
     */
    public function setArguments($args)
    {
        $this->arguments = $args;
    }

    /**
     * Sets the user supplied options.
     *
     * @param $opts
     */
    public function setOptions($opts)
    {
        $this->options = $opts;
    }

    /**
     * Adds a single argument (supplied as an array).
     *
     * @param $array
     */
    public function addArgument($array)
    {
        $this->arguments = $this->arguments + $array;
    }

    /**
     * Adds a single option (supplied as an array).
     *
     * @param $array
     */
    public function addOption($array)
    {
        $this->options = $this->options + $array;
    }

    /**
     * Returns an array of data that should be merged with the rendering environment.
     *
     * @return array
     */
    public function collect()
    {
        return [
            'user_options' => $this->options,
            'user_args'    => $this->arguments
        ];
    }

}