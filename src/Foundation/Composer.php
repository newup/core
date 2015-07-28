<?php

namespace NewUp\Foundation;

use NewUp\Contracts\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Composer
{

    /**
     * The working path.
     *
     * @var string
     */
    protected $workingPath;

    /**
     * The filesystem implementation.
     *
     * @var \NewUp\Contracts\Filesystem\\Filesystem
     */
    protected $files;

    public function __construct(Filesystem $filesystem)
    {
        $this->files = $filesystem;
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if ($this->files->exists($this->workingPath.'/composer.phar')) {
            return '"'.PHP_BINARY.'" composer.phar';
        }

        return 'composer';
    }

    /**
     * Sets the working path.
     *
     * @param $workingPath
     */
    public function setWorkingPath($workingPath)
    {
        $this->workingPath = $workingPath;
    }

    /**
     * Gets the working path.
     *
     * @return string
     */
    public function getWorkingPath()
    {
        return $this->workingPath;
    }

    /**
     * Get a new Symfony process instance.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess()
    {
        return (new Process('', $this->workingPath))->setTimeout(null);
    }

}