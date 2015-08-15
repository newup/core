<?php

namespace NewUp\Foundation\Composer;

use Illuminate\Contracts\Logging\Log;
use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Foundation\Composer\Exceptions\InvalidInstallationDirectoryException;
use NewUp\Foundation\Composer\Exceptions\PackageInstallationException;
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
     * The initial working directory.
     *
     * @var string
     */
    protected $initialDirectory;

    /**
     * The filesystem implementation.
     *
     * @var \NewUp\Contracts\Filesystem\\Filesystem
     * protected $files;
     */

    /**
     * The current number of attempts the installer has made
     * to prepare a given directory for package installation.
     *
     * @var int
     */
    protected $installationAttempts = 0;

    /**
     * The maximum number of attempts to make to prepare a given
     * directory.
     *
     * @var int
     */
    protected $breakAtInstallationAttempt = 2;

    /**
     * The Log implementation instance.
     *
     * @var Log
     */
    protected $log;

    public function __construct(Filesystem $filesystem, Log $logger)
    {
        $this->files            = $filesystem;
        $this->initialDirectory = getcwd();
        $this->log              = $logger;
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
     * @param $forceOptions
     * @return string
     */
    private function prepareOptions($options, $forceOptions = [])
    {
        $optionString = '';

        foreach ($forceOptions as $option => $value) {
            if (is_numeric($option)) {
                $options[$value] = null;
            } else {
                $options[$option] = $value;
            }
        }

        foreach ($options as $option => $value) {

            if (is_null($value)) {
                $optionString .= ' ' . $option;
            } else {
                $optionString .= ' ' . $option . '=' . $value;
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
            $this->files->makeDirectory($directory . DIRECTORY_SEPARATOR, 0755, true);

            return;
        }

        $this->files->deleteDirectory($directory, true);
        $this->checkInstallationDirectory($directory);
    }

    /**
     * Checks the installation directory to make sure it is ready.
     *
     * @throws InvalidInstallationDirectoryException
     * @throws PackageInstallationException
     * @param $directory
     */
    private function checkInstallationDirectory($directory)
    {
        if ($this->installationAttempts >= $this->breakAtInstallationAttempt) {
            throw new PackageInstallationException(null, "The package template could not be installed.");
        }

        $files       = $this->files->allFiles($directory);
        $directories = $this->files->directories($directory);

        if (count($files) == 0 && count($directories) == 0) {
            return;
        }

        $directoriesReadable = true;
        $filesReadable       = true;

        foreach ($directories as $directory) {
            if (!$this->files->isReadable($directory)) {
                $directoriesReadable = false;
            }
        }

        foreach ($files as $file) {
            if (!$this->files->isReadable($file->getPathname())) {
                $filesReadable = false;
            }
        }

        if (!$directoriesReadable || !$filesReadable) {
            throw new InvalidInstallationDirectoryException(null,
                "The installation directory ({$directory}) is not empty and could not be cleared due to a permissions issue. Please manually remove all files from the directory and try again.");
        }

        if (!$this->files->isWritable($directory)) {
            throw new InvalidInstallationDirectoryException(null,
                "The installation directory ({$directory}) is not writeable by the NewUp process.");
        }

        // At this point, there is no clear reason why the preparation
        // did not succeed. Because of this, we will try again.
        $this->installationAttempts++;
        $this->prepareInstallationDirectory($directory);
    }

    /**
     * Gets the message when Composer throws an exception.
     *
     * @param $errorMessage
     * @return string
     */
    private function parseComposerErrorMessage($errorMessage)
    {
        // Split the error message into an array of lines.
        $errorLines = preg_split("/\r\n|\n|\r/", $errorMessage);
        if (isset($errorLines[4])) {
            return trim($errorLines[4]);
        }

        return '';
    }

    /**
     * Installs a Composer package, placing it in NewUp's template storage.
     *
     * @param        $packageName
     * @param  array $options
     * @throws PackageInstallationException
     * @return bool
     */
    public function installPackage($packageName, $options = [])
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer() . ' create-project ' . $packageName . ' "' .
                                      $this->workingPath . '" ' .
                                      $this->prepareOptions($options, ['--no-ansi', '--no-install'])));
        $this->prepareInstallationDirectory($this->workingPath);

        $process->run();

        if ($process->isSuccessful() == false) {
            $composerError = $this->parseComposerErrorMessage($process->getErrorOutput());
            throw new PackageInstallationException($process->getErrorOutput(),
                "There was an error installing the package: {$packageName}" . PHP_EOL .
                'Composer is reporting the following error:' . PHP_EOL . '--> ' . $composerError);
        }

        return true;
    }

    /**
     * Updates the packages dependencies by running "composer update".
     *
     * @throws PackageInstallationException
     * @param array $options
     * @return bool
     */
    public function updatePackageDependencies($options = [])
    {
        $process = $this->getProcess();
        $process->setCommandLine(trim($this->findComposer() . ' update ' .
                                      $this->prepareOptions($options, ['--no-progress'])));

        chdir($this->workingPath);
        $process->run();

        if ($process->isSuccessful() == false) {
            $composerError = remove_ansi($process->getErrorOutput());
            throw new PackageInstallationException($process->getErrorOutput(),
                "There was an error updating the dependencies." . PHP_EOL .
                'Composer is reporting the following error(s):' . PHP_EOL . '--> ' . $composerError);
        }

        chdir($this->initialDirectory);

        return true;
    }

}