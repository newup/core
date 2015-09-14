<?php

namespace NewUp\Support\Testing\FilesystemVirtualization;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

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
        return vfsStream::url($this->virtualPath.'/'.$path);
    }

}