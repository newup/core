#!/usr/bin/env php
<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

use NewUp\Foundation\Application;
use NewUp\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Output\ConsoleOutput;

$autoLoader = require __DIR__ . '/bootstrap/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

Application::$loader = &$autoLoader;
ConsoleApplication::$output = new ConsoleOutput;
ConsoleApplication::$input = new \Symfony\Component\Console\Input\ArgvInput;

/*
|--------------------------------------------------------------------------
| Run The Artisan Application
|--------------------------------------------------------------------------
|
| When we run the console application, the current CLI command will be
| executed in this console and the response sent back to a terminal
| or another output device for the developers. Here goes nothing!
|
*/

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$status = $kernel->handle(
    ConsoleApplication::$input,
    ConsoleApplication::$output
);

/*
|--------------------------------------------------------------------------
| Shutdown The Application
|--------------------------------------------------------------------------
|
| Once Artisan has finished running. We will fire off the shutdown events
| so that any final work may be done by the application before we shut
| down the process. This is the last thing to happen to the request.
|
*/

$kernel->terminate(ConsoleApplication::$input, $status);

exit($status);