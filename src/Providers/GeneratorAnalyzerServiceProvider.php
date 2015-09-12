<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Contracts\IO\FileTreeGenerator;
use NewUp\Contracts\IO\DirectoryAnalyzer as DirectoryAnalyzerContract;
use NewUp\Filesystem\DirectoryAnalyzer;
use NewUp\Filesystem\Generators\TreeGenerator;

class GeneratorAnalyzerServiceProvider extends ServiceProvider
{

    protected $singletonClassMap = [
        FileTreeGenerator::class => TreeGenerator::class,
        DirectoryAnalyzerContract::class => DirectoryAnalyzer::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->singletonClassMap as $abstract => $concrete)
        {
            $this->app->singleton($abstract, function() use ($concrete)
            {
                return $this->app->make($concrete);
            });
        }
    }

}