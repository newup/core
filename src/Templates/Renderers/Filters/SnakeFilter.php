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

    public function getOperator()
    {
        return function ($string, $delimiter = '_') {
            return Str::snake($string, $delimiter);
        };
    }


}