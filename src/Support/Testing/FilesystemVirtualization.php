<?php

namespace NewUp\Support\Testing;

use org\bovigo\vfs\vfsStream;

trait FilesystemVirtualization
{

    /**
     * The vfsStream instance.
     *
     * @var vfsStream
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
     * Frees the vfs variable and unsets it.
     */
    public function tearDownVfs()
    {
        $this->vfs = null;
        unset($this->vfs);
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
        return vfsStream::url($path);
    }

}