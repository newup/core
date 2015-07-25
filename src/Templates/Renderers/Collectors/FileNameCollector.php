<?php

namespace NewUp\Templates\Renderers\Collectors;

use NewUp\Contracts\DataCollector;

class FileNameCollector implements DataCollector
{

    /**
     * The collected file names.
     *
     * The file names are stored in this array where
     * the original file name is the key and the
     * final form of that file path is the value.
     *
     * @var array
     */
    protected $fileNames = [];

    /**
     * Adds a file name to the list (supplied as an array).
     *
     * @param $array
     */
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