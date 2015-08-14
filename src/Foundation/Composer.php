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
        if ($this->files->exists($this->workingPath . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
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

    /**
     * Prepares the options string.
     *
     * @param $options
     * @return string
     */
    private function prepareOptions($options)
    {
        $optionString = '';

        if (!array_key_exists('--do-install', $options)) {
            $options['--no-install'] = null;
        }

        foreach ($options as $option => $value) {

            if ($option == '--do-install') {
                continue;
            }

            if (is_null($value)) {
                $optionString .= ' ' . $option;
            } else {
                $optionString .= ' ' . $option . ' ' . $value;
            }
        }

        return $optionString;
    }

    /**
     * Prepares the installation directory.
     *
     * This method will create the directory if it does
     * not exist and will ensure that the directory is
     * empty if it does.
     *
     * @param $directory
     */
    private function prepareInstallationDirectory($directory)
    {
        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory);

            return;
        }

        $this->files->deleteDirectory($directory, true);
    }

    public function installPackage($packageName, $options = [])
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer() . ' create-project ' . $packageName . ' "' .
                                      $this->workingPath . '" ' . $this->prepareOptions($options)));
        $this->prepareInstallationDirectory($this->workingPath);
        $process->run();

        if ($process->isSuccessful() == false) {
            // TODO:: HANDLE ERROR
        }

        // TODO:: HANDLE SUCCESS
    }

}