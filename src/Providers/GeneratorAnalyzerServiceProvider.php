<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;
use NewUp\Contracts\IO\FileTreeGenerator;
use NewUp\Contracts\IO\DirectoryAnalyzer as DirectoryAnalyzerContract;
use NewUp\Templates\Analyzers\DirectoryAnalyzer;
use NewUp\Templates\Generators\FileSystemTreeGenerator;

class GeneratorAnalyzerServiceProvider extends ServiceProvider
{

    protected $singletonClassMap = [
        FileTreeGenerator::class => FileSystemTreeGenerator::class,
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