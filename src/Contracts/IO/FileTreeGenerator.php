<?php

namespace NewUp\Contracts\IO;

interface FileTreeGenerator
{

    /**
     * Adds a path that should be ignored when generating the structure.
     *
     * @param $path
     */
    public function addIgnoredPath($path);

    /**
     * Returns an array of all ignored paths.
     *
     * @return array
     */
    public function getIgnoredPaths();

    /**
     * Removes all ignored paths.
     */
    public function resetIgnoredPaths();

    /**
     * Removes the specified ignored path.
     *
     * @param $path
     */
    public function removeIgnoredPath($path);

    /**
     * Adds a path that should automatically be removed after the final
     * directory structure has been generated.
     *
     * @param $path
     */
    public function addAutomaticallyRemovedPath($path);

    /**
     * Returns an array of all automatically removed paths.
     *
     * @return array
     */
    public function getAutomaticallyRemovedPaths();

    /**
     * Removes all automatically-removed paths.
     */
    public function resetAutomaticallyRemovedPaths();

    /**
     * Removes the specified automatically-removed path.
     * @param $path
     */
    public function removeAutomaticallyRemovedPath($path);

    /**
     * Adds paths to the file tree generator.
     *
     * @param $paths
     */
    public function addPaths($paths);

    /**
     * Gets the paths that will be created.
     *
     * @return array
     */
    public function getPaths();

    /**
     * Creates the file tree in the given directory.
     *
     * @param $destinationDirectory
     * @return void
     */
    public function generate($destinationDirectory);

    /**
     * Sets whether or not the tree generator is inside
     * a template directory.
     *
     * @param $inside
     */
    public function setInsideTemplateDirectory($inside);

}