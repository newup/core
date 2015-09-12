<?php

namespace NewUp\Templates\Renderers\Filters;

use Illuminate\Support\Str;

class SnakeFilter extends Filter
{

    /**
     * The name of the filter.
     *
     * @var string
     */
    protected $name = 'snake';

    /**
     * Gets the function that is passed to the Twig environment.
     *
     * @return \Closure
     */
    public function getFilter()
    {
        return function ($string, $delimiter = '_') {
            return Str::snake($string, $delimiter);
        };
    }


}