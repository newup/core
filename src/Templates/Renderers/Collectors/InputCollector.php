<?php

namespace Newup\Templates\Renderers\Collectors;

use NewUp\Contracts\DataCollector;

class InputCollector implements DataCollector
{

    protected $options = [];

    protected $arguments = [];

    public function setArguments($args)
    {
        $this->arguments = $args;
    }

    public function setOptions($opts)
    {
        $this->options = $opts;
    }

    public function addArgument($array)
    {
        $this->arguments = $this->arguments + $array;
    }

    public function addOptioni($array)
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