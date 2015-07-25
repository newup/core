<?php

namespace NewUp\Contracts\IO;

use NewUp\Exceptions\InvalidPathException;

interface DirectoryAnalyzer
{

    /**
     * The directory to analyze.
     *
     * @param  $directory
     * @return array
     * @throws InvalidPathException
     */
    public function analyze($directory);

}