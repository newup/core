<?php

namespace NewUp\Templates\Renderers\Collectors;

use NewUp\Contracts\DataCollector;

class FileNameCollector implements DataCollector
{

    protected $fileNames = [];

    public function addFileNames($array)
    {
        $this->fileNames = $this->fileNames + $array;
    }

    /**
     * Returns an array of data that should be merged with the rendering environment.
     *
     * @return array
     */
    public function collect()
    {
        return ['sys_pathNames' => $this->fileNames];
    }

}