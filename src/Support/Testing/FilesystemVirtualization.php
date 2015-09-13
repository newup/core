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
        dd($this->virtualPath);
    }

    /**
     * Frees the vfs variable and unsets it.
     */
    public function tearDownVfs()
    {
        $this->vfs = null;
        unset($this->vfs);
    }

}