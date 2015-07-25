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
     * Gets the name of the filter.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}