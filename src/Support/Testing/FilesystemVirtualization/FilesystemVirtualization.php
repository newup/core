<?php

namespace NewUp\Support\Testing\FilesystemVirtualization;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamContent;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;

trait FilesystemVirtualization
{

    /**
     * The vfsStream instance.
     *
     * @var vfsStreamDirectory
     */
    protected $vfs;

    /**
     * Sets up the vfs instance.
     */
    public function setUpVfs()
    {
        $this->vfs = vfsStream::setup($this->virtualPath);
    }

    /**
     * Gets the vfsStreamDirectory instance.
     *
     * @return \org\bovigo\vfs\vfsStreamDirectory
     */
    public function getVfs()
    {
        return $this->vfs;
    }

    /**
     * Frees the vfs variable and unsets it.
     */
    public function tearDownVfs()
    {
        unset($this->vfs);
        $this->vfs = null;
    }

    /**
     * Gets a vfsStream URL.
     *
     * @param $path
     *
     * @return string
     */
    public function getPath($path)
    {
        return vfsStream::url($this->virtualPath . '/' . $path);
    }

    /**
     * Gets the total number of virtual files.
     *
     * @return int
     */
    public function getFileCount()
    {
        $this->assertVfsHasBeenSetUp();

        return count($this->vfs->getChildren());
    }

    /**
     * Gets a virtual file at a specified index.
     *
     * @param $index
     *
     * @return \org\bovigo\vfs\vfsStreamContent
     */
    public function getFileAtIndex($index)
    {
        return $this->vfs->getChildren()[$index];
    }

    /**
     * Gets a virtual file by name.
     *
     * @param $name
     *
     * @return \org\bovigo\vfs\vfsStreamContent
     */
    public function getFileByName($name)
    {
        return $this->vfs->getChild($name);
    }

    /**
     * Virtualizes an array of paths.
     *
     * @param $paths
     */
    public function virtualize($paths)
    {
        $this->assertVfsHasBeenSetUp();

        if (!is_array($paths)) {
            $paths = (array)$paths;
        }

        foreach ($paths as $path) {
            if (is_string($path)) {
                $this->vfs->addChild(vfsStream::newFile($path));
            } else if ($path instanceof vfsStreamContent) {
                $this->vfs->addChild($path);
            }
        }
    }

    /**
     * Creates and appends a given structure to the virtual file system.
     *
     * @param $structure
     */
    public function virtualizeStructure($structure)
    {
        $this->assertVfsHasBeenSetUp();

        vfsStream::create($structure, $this->vfs);
    }

    /**
     * Asserts that the vfs instance has been created.
     *
     * @throws \RuntimeException
     */
    private function assertVfsHasBeenSetUp()
    {
        if ($this->vfs == null || ($this->vfs instanceof vfsStreamDirectory) == false) {
            throw new \RuntimeException('You must call the setUpVfs() method before executing test methods.');
        }
    }

}