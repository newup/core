<?php

namespace NewUp\Templates\Renderers\Filters;

use Illuminate\Support\Str;

class CamelFilter extends Filter
{

    /**
     * The name of the filter.
     *
     * @var string
     */
    protected $name = 'camel';

    /**
     * Gets the function that is passed to the Twig environment.
     *
     * @return \Closure
     */
    public function getOperator()
    {
        return function ($string) {
            return Str::camel($string);
        };
    }


}