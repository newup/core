<?php

namespace NewUp\Console;

use Illuminate\Foundation\Console\Kernel as LaravelKernel;
use NewUp\Console\Application as NewUpApplication;
use Illuminate\Foundation\Bootstrap\DetectEnvironment;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use NewUp\Foundation\Bootstrap\ConfigureLogging;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\SetRequestForConsole;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Bootstrap\BootProviders;

abstract class BaseKernel extends LaravelKernel
{

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        DetectEnvironment::class,
        LoadConfiguration::class,
        ConfigureLogging::class,
        HandleExceptions::class,
        SetRequestForConsole::class,
        RegisterProviders::class,
        BootProviders::class,
    ];

    /**
     * Get the Artisan application instance.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            return $this->artisan = (new NewUpApplication($this->app, $this->events))
                ->resolveCommands($this->getCommands());
        }

        return $this->artisan;
    }

    protected function getCommands()
    {
        return $this->commands;
    }

}