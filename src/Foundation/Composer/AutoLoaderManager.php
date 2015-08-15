<?php

namespace NewUp\Foundation\Composer;

use Illuminate\Contracts\Logging\Log;
use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Foundation\Application;
use NewUp\Templates\Generators\PathNormalizer;

class AutoLoaderManager
{

    use PathNormalizer;

    /**
     * The Filesystem implementation.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The Log implementation instance.
     *
     * @var Log
     */
    protected $log;

    public function __construct(Filesystem $files, Application $app, Log $logger)
    {
        $this->files = $files;
        $this->app   = $app;
        $this->log   = $logger;
    }

    /**
     * Autoloads a packages dependencies by merging them with the current auto-loader.
     *
     * @param $directory
     */
    public function mergePackageLoader($directory)
    {
        if ($vendor = $this->findVendor($directory)) {
            $this->mergeComposerNamespaces($vendor);
            $this->mergeComposerPsr4($vendor);
            $this->mergeComposerClassMap($vendor);
        }
    }

    /**
     * Merges the dependencies namespaces.
     *
     * @param $vendor
     */
    private function mergeComposerNamespaces($vendor)
    {
        if ($namespaceFile = $this->findComposerDirectory($vendor, 'autoload_namespaces.php')) {
            $this->log->debug('Located autoload_namespaces.php file', ['path' => $namespaceFile]);
            $map = require $namespaceFile;
            foreach ($map as $namespace => $path) {
                $this->log->debug('Autoloading namespace', ['namespace' => $namespace, 'path' => $path]);
                $this->app->getLoader()->set($namespace, $path);
            }
        }
    }

    /**
     * Merges the dependencies PSR-4 namespaces.
     *
     * @param $vendor
     */
    private function mergeComposerPsr4($vendor)
    {
        if ($psr4Autoload = $this->findComposerDirectory($vendor, 'autoload_psr4.php')) {
            $this->log->debug('Located autoload_psr4.php file', ['path' => $psr4Autoload]);
            $map = require $psr4Autoload;
            foreach ($map as $namespace => $path) {
                $this->log->debug('Autoloading PSR-4 namespace', ['namespace' => $namespace, 'path' => $path]);
                $this->app->getLoader()->setPsr4($namespace, $path);
            }
        }
    }

    /**
     * Merges the dependencies class maps.
     *
     * @param $vendor
     */
    private function mergeComposerClassMap($vendor)
    {
        if ($composerClassMap = $this->findComposerDirectory($vendor, 'autoload_classmap.php')) {
            $this->log->debug('Located autoload_classmap.php file', ['path' => $composerClassMap]);
            $classMap = require $composerClassMap;
            if ($classMap) {
                $this->app->getLoader()->addClassMap($classMap);
            }
        }
    }

    /**
     * Finds the composer directory.
     *
     * Returns false if the directory does not exist.
     *
     * @param         $vendor The vendor directory.
     * @param  string $file   An optional file to look for.
     * @return bool|string
     */
    private function findComposerDirectory($vendor, $file = '')
    {
        $composerDirectory = $this->normalizePath($vendor . '/composer/' . $file);

        if ($this->files->exists($composerDirectory)) {
            return $composerDirectory;
        }

        return false;
    }

    /**
     * Gets the vendor directory location if it exists.
     *
     * Returns false if the vendor directory does not exist.
     *
     * @param $directory
     * @return bool|string
     */
    private function findVendor($directory)
    {
        $vendorDirectory = $this->normalizePath($directory . '/_newup_vendor');

        if ($this->files->exists($vendorDirectory) && $this->files->isDirectory($vendorDirectory)) {
            return $vendorDirectory;
        }

        return false;
    }

}