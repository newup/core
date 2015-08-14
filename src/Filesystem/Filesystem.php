<?php

namespace NewUp\Filesystem;

use Illuminate\Filesystem\Filesystem as LaravelFileSystem;
use NewUp\Contracts\Filesystem\Filesystem as FileSystemContract;
use Symfony\Component\Finder\Finder;

/**
 * Class Filesystem
 *
 * This file system behaves just like the FileSystem class provided by
 * the Laravel framework, except that the `allFiles()` method does not
 * ignore dot files.
 *
 * @package NewUp\Filesystem
 */
class Filesystem extends LaravelFileSystem implements FileSystemContract
{

    /**
     * Get all of the files from the given directory (recursive).
     *
     * This function does not ignore dot files.
     *
     * @param string $directory
     * @return array
     */
    public function allFiles($directory)
    {
        return iterator_to_array(Finder::create()->files()->ignoreDotFiles(false)->in($directory), false);
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param  string  $directory
     * @return array
     */
    public function directories($directory)
    {
        $directories = array();

        foreach (Finder::create()->in($directory)->directories()->ignoreVCS(false)->ignoreDotFiles(false)->ignoreUnreadableDirs(false)->depth(0) as $dir)
        {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }

    /**
     * Determine if the given path is readable.
     *
     * @param  string $path
     * @return mixed
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }


}