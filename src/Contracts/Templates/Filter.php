<?php

namespace NewUp\Contracts\Templates;

interface Filter
{

    /**
     * Gets the name of the filter.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the function that is passed to the rendering environment.
     *
     * @return \Closure
     */
    public function getFilter();

}