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

    public function getOperator()
    {
        return function ($string) {
            return Str::lower($string);
        };
    }


}