<?php

namespace NewUp\Templates\Renderers\Filters;

use Illuminate\Support\Str;

class SlugFilter extends Filter
{

    /**
     * The name of the filter.
     *
     * @var string
     */
    protected $name = 'slug';

    /**
     * Gets the function that is passed to the Twig environment.
     *
     * @return \Closure
     */
    public function getOperator()
    {
        return function ($string, $separator = '-') {
            return Str::slug($string, $separator);
        };
    }


}