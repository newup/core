<?php

namespace NewUp\Contracts;

interface DataCollector
{

    /**
     * Returns an array of data that should be merged with the rendering environment.
     *
     * @return array
     */
    public function collect();

}