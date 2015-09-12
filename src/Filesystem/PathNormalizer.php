<?php

namespace NewUp\Filesystem;

trait PathNormalizer
{

    /**
     * Normalizes the use of '/' and '\' in a path.
     *
     * @param  string $path
     * @param  bool   $removeTrailingSeparator
     * @return string
     */
    private function normalizePath($path, $removeTrailingSeparator = false)
    {
        $newPath = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $newPath = str_replace('\\', DIRECTORY_SEPARATOR, $newPath);

        $newPath = str_replace(':'.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, '://', $newPath);

        if ($removeTrailingSeparator) {
            return rtrim($newPath, DIRECTORY_SEPARATOR);
        }

        return $newPath;
    }

}