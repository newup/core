<?php

namespace NewUp\Templates\Renderers\Filters;

use NewUp\Contracts\Templates\Filter as FilterContract;

abstract class Filter implements FilterContract
{

    /**
     * The name of the filter.
     *
     * @var string
     */
    protected $name;

    /**
     * Gets the function that is passed to the Twig environment.
     *
     * @return \Closure
     */
    public function getName()
    {
        return $this->name;
    }

}