<?php

namespace NewUp\Console\Commands\Tse;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use NewUp\Contracts\Filesystem\Filesystem;
use NewUp\Contracts\Templates\SearchableStorageEngine;

class Analyze extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tse:analyze';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyzes the template storage for possible corruptions';

    /**
     * The SearchableStorageEngine implementation instance.
     *
     * @var SearchableStorageEngine
     */
    protected $templateStorageEngine;

    /**
     * The Filesystem implementation instance.
     *
     * @var Filesystem
     */
    protected $files;

    public function __construct(SearchableStorageEngine $templateStorageEngine, Filesystem $files)
    {
        parent::__construct();
        $this->templateStorageEngine = $templateStorageEngine;
        $this->files                 = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if ( ! $this->confirm("Are you sure you want to run the analysis tool? This tool will make modifications to the template storage.".PHP_EOL."While it is intended to clean up the template storage, it could potentially fail. [yes|no]", false))
        {
            $this->comment('Analysis aborted by user');
            return;
        }

        $this->info('Starting analysis of template store...');
        $this->line('Store located at ' . $this->templateStorageEngine->getStoragePath());
        $vendors      = $this->templateStorageEngine->getInstalledVendors();
        $packages     = $this->templateStorageEngine->getInstalledPackages(true);
        $packageCount = 0;
        foreach ($packages as $package) {
            $packageCount += count($package['packages']);
        }
        $this->line('Found ' . count($vendors) . ' vendors and ' . $packageCount . ' packages...');


        $this->comment(PHP_EOL . 'Searching for failed update processes...');
        $failedUpdateProcesses     = [];
        $garbagePackageDirectories = [];

        foreach ($packages as $vp) {
            foreach ($vp['packages'] as $package) {
                if (Str::endsWith($package['path'], '_{updating_in_progress}')) {
                    $garbagePackageDirectories[] = $package;
                    $this->line('Identified package as possible garbage ' . $package['path']);
                } else {
                    if ($this->files->exists($package['path'] . DIRECTORY_SEPARATOR . '_newup_update_initiated')) {
                        $failedUpdateProcesses[] = $package;
                        $this->line('Identified failed package update process ' . $package['path']);
                    }
                }
            }
        }

        $this->line('Found ' . count($failedUpdateProcesses) . ' failed update processes');
        $this->line('Found ' . count($garbagePackageDirectories) . ' update directories ready for cleanup');

        $this->comment(PHP_EOL . 'Analyzing search results...');
        $recoverablePackages    = [];
        $nonRecoverablePackages = [];

        foreach ($failedUpdateProcesses as $failedUpdate) {
            if ($this->files->exists($failedUpdate['path'] . '_{updating_in_progress}')) {
                $this->line('Identified package as recoverable '.$failedUpdate['path']);
                $recoverablePackages[] = $failedUpdate;
            } else {
                $this->line('Identified a non-recoverable package '.$failedUpdate['path']);
                $nonRecoverablePackages[] = $failedUpdate;
            }
        }

        $this->line('Found '.count($recoverablePackages).' recoverable failed update processes');
        $this->line('Found '.count($nonRecoverablePackages).' non-recoverable failed update processes');

        $this->comment(PHP_EOL.'Cleaning up non-recoverable update processes...');
        if (count($nonRecoverablePackages) > 0) {
            foreach ($nonRecoverablePackages as $nonRecoverablePackage) {
                $this->files->deleteDirectory($nonRecoverablePackage['path'], false);
                $this->line('Cleaning up directory '.$nonRecoverablePackage['path']);
            }
        } else {
            $this->line('Nothing to clean up');
        }


        $this->comment(PHP_EOL.'Cleaning up failed update processes...');
        if (count($garbagePackageDirectories) > 0 ) {
            foreach ($garbagePackageDirectories as $garbage) {
                $originalPackagePath = $garbage['path'];
                $originalPackagePath = str_replace('_{updating_in_progress}', '', $originalPackagePath);

                if ($this->files->exists($originalPackagePath)) {
                    if ($this->files->exists($originalPackagePath.DIRECTORY_SEPARATOR.'_newup_update_initiated')) {
                        $this->comment('Recovering failed update process for '.$originalPackagePath);
                        $this->line('Removing failed update files at '.$originalPackagePath);
                        $this->files->deleteDirectory($originalPackagePath);
                        $this->line('Recovering files...');
                        $this->files->copyDirectory($garbage['path'], $originalPackagePath);
                        if ($this->files->exists($originalPackagePath)) {
                            $this->info('Recovered files at '.$originalPackagePath);
                        } else {
                            $this->error('Could not recover files at '.$originalPackagePath);
                        }
                        $this->line('Cleaning up garbage files at '.$garbage['path']);
                        $this->files->deleteDirectory($garbage['path']);
                    } else {
                        // Original package path does not exist, remove trash.
                        $this->line('Removing garbage '.$garbage['path'].' because '.$originalPackagePath.' no longer exists');
                        $this->files->deleteDirectory($garbage['path']);
                    }
                } else {
                    // Original package path does not exist, remove trash.
                    $this->line('Removing garbage '.$garbage['path'].' because '.$originalPackagePath.' no longer exists');
                    $this->files->deleteDirectory($garbage['path']);
                }
            }
        } else {
            $this->line('Nothing to clean up');
        }

        $this->comment(PHP_EOL.'Analysis complete');

    }

}