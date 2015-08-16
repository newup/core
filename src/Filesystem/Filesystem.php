<?php

namespace NewUp\Filesystem;

use Illuminate\Filesystem\Filesystem as LaravelFileSystem;
use NewUp\Contracts\Filesystem\Filesystem as FileSystemContract;
use Symfony\Component\Finder\Finder;
use FilesystemIterator;

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
        return iterator_to_array(Finder::create()->files()->ignoreDotFiles(false)->ignoreVCS(false)->in($directory), false);
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
     * Recursively delete a directory.
     *
     * The directory itself may be optionally preserved.
     *
     * @param  string  $directory
     * @param  bool    $preserve
     * @return bool
     */
    public function deleteDirectory($directory, $preserve = false)
    {
        if ( ! $this->isDirectory($directory)) return false;

        $items = new FilesystemIterator($directory);

        foreach ($items as $item)
        {
            // If the item is a directory, we can just recurse into the function and
            // delete that sub-directory otherwise we'll just delete the file and
            // keep iterating through each file until the directory is cleaned.
            if ($item->isDir() && ! $item->isLink())
            {
                $this->deleteDirectory($item->getPathname());
            }

            // If the item is just a file, we can go ahead and delete it since we're
            // just looping through and waxing all of the files in this directory
            // and calling directories recursively, so we delete the real path.
            else
            {
                @chmod($item->getPathname(), 0755);
                $this->delete($item->getPathname());
            }
        }

        if ( ! $preserve) @rmdir($directory);

        return true;
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