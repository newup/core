<?php

namespace NewUp\Filesystem\Generators;

use Illuminate\Support\Str;
use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Contracts\IO\FileTreeGenerator;
use NewUp\Filesystem\PathNormalizer;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileSystemTreeGenerator
 *
 * This generator is used to create the actual directory structure of the
 * generated package. It will also create empty files that will act as
 * placeholders for additional steps.
 *
 * @package NewUp\Templates\Generators
 */
class TreeGenerator implements FileTreeGenerator
{

    use PathNormalizer;

    /**
     * A collection of paths and directories to create.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * The file system instance.
     *
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * An array of paths that should be ignored by the generator.
     *
     * @var array
     */
    protected $ignoredPaths = [];

    /**
     * An array of paths that are always ignored by the generator.
     *
     * This array is automatically generated and is not generally
     * modified by end users.
     *
     * @var array
     */
    protected $automaticallyResolvedIgnoredPaths = [];

    /**
     * Determines if the generator is inside a '_template' directory.
     *
     * @var bool
     */
    protected $insideTemplateDirectory = false;

    /**
     * An array of paths that should be automatically removed by the generator.
     *
     * @var array
     */
    protected $automaticallyRemovedPaths = [];

    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Adds paths to the file tree generator.
     *
     * The paths added using this method should already be processed, meaning that
     * the final path is already free of template processors, placeholders, etc.
     *
     * @param $paths
     * @return mixed
     */
    public function addPaths($paths)
    {
        $this->paths = $this->paths + $paths;
    }

    /**
     * Converts all instances of '\' or '/' to the system directory separator.
     */
    private function normalizePaths()
    {
        foreach ($this->paths as $key => $path) {
            $this->paths[$key] = $this->normalizePath($path);
        }
    }

    /**
     * Sorts the paths so that the most specific paths are listed first.
     *
     * @return array
     */
    private function sortPaths()
    {
        $this->normalizePaths();

        uasort($this->paths, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }

            return (substr_count($a['path'], DIRECTORY_SEPARATOR) >
                substr_count($b['path'], DIRECTORY_SEPARATOR)) ? -1 : 1;
        });

        return $this->paths;
    }

    /**
     * Gets the paths that will be created.
     *
     * @return mixed
     */
    public function getPaths()
    {
        return $this->sortPaths();
    }

    /**
     * Removes all files and directories that are set to be
     * automatically removed.
     *
     * @param  $destinationDirectory
     * @return array
     */
    private function removeFilesAndDirectories($destinationDirectory)
    {
        $currentStructure = $this->fileSystem->allFiles($destinationDirectory);

        $removedPaths = [];

        foreach ($currentStructure as $file) {
            /* @var SplFileInfo $file */
            $directoryPath =
                $this->normalizePath($destinationDirectory . DIRECTORY_SEPARATOR . $file->getRelativePath());
            $fullPath      =
                $this->normalizePath($destinationDirectory . DIRECTORY_SEPARATOR . $file->getRelativePathname());

            if ($this->shouldBeRemoved($directoryPath)) {
                $removedPaths[] = $directoryPath;
                $this->fileSystem->deleteDirectory($directoryPath, false);
            }

            if ($this->shouldBeRemoved($fullPath)) {
                $removedPaths[] = $fullPath;
                $this->fileSystem->delete($fullPath);
            }

        }

        return $removedPaths;
    }

    /**
     * Creates the file tree in the given directory.
     *
     * @param  $destinationDirectory
     * @return array
     */
    public function generate($destinationDirectory)
    {
        $destinationDirectory = $destinationDirectory;

        $generatedPaths = [];

        $this->resolveAutomaticallyIgnoredPaths($destinationDirectory);

        foreach ($this->getPaths() as $pathKey => $path) {
            $fullPath = $destinationDirectory . DIRECTORY_SEPARATOR . $path['path'];

            if (!$this->shouldBeIgnored($path['path'])) {
                if ($path['type'] == 'dir') {
                    $this->fileSystem->makeDirectory($fullPath, 0755, true, true);
                } else {
                    // There are two steps here:
                    // 1st: Recursively create the directory structure for the file (it might not exist)
                    // 2nd: Create an empty file using `touch()` since we are guaranteed the directory structure exists.
                    $this->fileSystem->makeDirectory(dirname($fullPath), 0755, true, true);
                    touch($fullPath);
                }

                $generatedPaths[$pathKey] = ($path + ['full' => $fullPath]);
            }
        }

        $this->removeFilesAndDirectories($destinationDirectory);

        return $generatedPaths;
    }

    /**
     * Resolves the automatically ignored paths.
     */
    private function resolveAutomaticallyIgnoredPaths()
    {
        foreach ($this->paths as $path) {
            if (is_array($path)) {
                $path = $path['home'];
            }
            if (!Str::endsWith($path, $this->normalizePath('_template/'))) {
                $ignoredPath = $this->normalizePath(realpath($path.'/composer.json'));

                if (!in_array($ignoredPath, $this->automaticallyResolvedIgnoredPaths)) {
                    $this->automaticallyResolvedIgnoredPaths[] = $ignoredPath;
                }
            }
        }

        // Add the '_newup' directory.
        $this->automaticallyResolvedIgnoredPaths[] = $this->normalizePath('_newup\*');
    }

    /**
     * @param $path
     *
     * @return bool
     */
    private function isAutomaticallyIgnored($path)
    {

        if (!$this->insideTemplateDirectory) {
            // Determine if the provided file is a composer.json file
            // that should be ignored automatically.
            if (!Str::startsWith('_template', $path) && $path == 'composer.json') {
                return true;
            }
        }

        foreach ($this->automaticallyResolvedIgnoredPaths as $ignoredPath) {
            if (Str::is($this->normalizePath($ignoredPath), $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if a path should be ignored.
     *
     * @param  $path
     * @return bool
     */
    private function shouldBeIgnored($path)
    {
        $path = $this->normalizePath($path);

        if ($this->isAutomaticallyIgnored($path)) {
            return true;
        }

        foreach ($this->ignoredPaths as $ignoredPath) {
            if (Str::is($this->normalizePath($ignoredPath), $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if a path should be removed.
     *
     * @param  $path
     * @return bool
     */
    private function shouldBeRemoved($path)
    {
        $path = $this->normalizePath($path);

        // Always remove "newup.keep" files.
        if (Str::is('*newup.keep', $path)) {
            return true;
        }

        foreach ($this->automaticallyRemovedPaths as $removedPath) {
            if (Str::is($this->normalizePath($removedPath), $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds a path that should be ignored when generating the structure.
     *
     * @param $path
     */
    public function addIgnoredPath($path)
    {
        $this->ignoredPaths[] = $path;
    }

    /**
     * Returns an array of all ignored paths.
     *
     * @return array
     */
    public function getIgnoredPaths()
    {
        return $this->ignoredPaths;
    }

    /**
     * Removes all ignored paths.
     */
    public function resetIgnoredPaths()
    {
        $this->ignoredPaths = [];
    }

    /**
     * Removes the specified ignored path.
     *
     * @param $path
     */
    public function removeIgnoredPath($path)
    {
        array_remove_value($this->ignoredPaths, $path);
        $this->ignoredPaths = array_values($this->ignoredPaths);
    }

    /**
     * Adds a path that should automatically be removed after the final
     * directory structure has been generated.
     *
     * @param $path
     */
    public function addAutomaticallyRemovedPath($path)
    {
        $this->automaticallyRemovedPaths[] = $path;
    }

    /**
     * Returns an array of all automatically removed paths.
     *
     * @return array
     */
    public function getAutomaticallyRemovedPaths()
    {
        return $this->automaticallyRemovedPaths;
    }

    /**
     * Removes all automatically-removed paths.
     */
    public function resetAutomaticallyRemovedPaths()
    {
        $this->automaticallyRemovedPaths = [];
    }

    /**
     * Removes the specified automatically-removed path.
     *
     * @param $path
     */
    public function removeAutomaticallyRemovedPath($path)
    {
        array_remove_value($this->automaticallyRemovedPaths, $path);
        $this->automaticallyRemovedPaths = array_values($this->automaticallyRemovedPaths);
    }

    /**
     * Sets whether or not the tree generator is inside
     * a template directory.
     *
     * @param $inside
     */
    public function setInsideTemplateDirectory($inside)
    {
        $this->insideTemplateDirectory = $inside;
    }


}