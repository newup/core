<?php

namespace NewUp\Templates\Renderers\Filters;

use Illuminate\Support\Str;

class PluralFilter extends Filter
{

    /**
     * The name of the filter.
     *
     * @var string
     */
    protected $name = 'plural';

    /**
     * Gets the function that is passed to the Twig environment.
     *
     * @return \Closure
     */
    public function getFilter()
    {
        return function ($string, $count = 2) {
            return Str::plural($string, $count);
        };
    }


}