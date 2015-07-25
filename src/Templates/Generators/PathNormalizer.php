<?php

namespace NewUp\Templates\Generators;

trait PathNormalizer
{

    /**
     * Normalizes the use of '/' and '\' in a path.
     *
     * @param  $path
     * @return string
     */
    private function normalizePath($path)
    {
        $newPath = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $newPath = str_replace('\\', DIRECTORY_SEPARATOR, $newPath);

        $newPath = str_replace(':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, '://', $newPath);

        return $newPath;
    }

}