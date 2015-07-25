<?php

namespace NewUp\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorAnalyzerServiceProvider extends ServiceProvider
{

    protected $singletonClassMap = [
        'NewUp\Contracts\IO\FileTreeGenerator' => 'NewUp\Templates\Generators\FileSystemTreeGenerator',
        'NewUp\Contracts\IO\DirectoryAnalyzer' => 'NewUp\Templates\Analyzers\DirectoryAnalyzer'
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