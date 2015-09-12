<?php

namespace NewUp\Templates\Renderers\Filters;

use Illuminate\Support\Str;

class LowerFilter extends Filter
{

    /**
     * The name of the filter.
     *
     * @var string
     */
    protected $name = 'lower';

    /**
     * Gets the function that is passed to the Twig environment.
     *
     * @return \Closure
     */
    public function getFilter()
    {
        return function ($string) {
            return Str::lower($string);
        };
    }


}