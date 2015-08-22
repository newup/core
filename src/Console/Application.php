<?php

namespace NewUp\Console;

use Illuminate\Console\Application as LaravelConsoleApplication;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application as LaravelApplication;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\ArrayInput;

class Application extends LaravelConsoleApplication
{

    public static $output;
    public static $input;

    /**
     * Create a new Artisan console application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $laravel
     * @param  \Illuminate\Contracts\Events\Dispatcher      $events
     * @return Application
     */
    public function __construct(LaravelApplication $laravel, Dispatcher $events)
    {
        SymfonyApplication::__construct('NewUp', $laravel->version());
        $this->event   = $events;
        $this->laravel = $laravel;
        $this->setAutoExit(false);
        $this->setCatchExceptions(false);
        $events->fire('artisan.start', [$this]);
    }

    public function callWithSharedOutput($command, array $parameters = array())
    {
        $parameters['command'] = $command;

        return $this->find($command)->run(new ArrayInput($parameters), self::$output);
    }

}