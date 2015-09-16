<?php

$app = new NewUp\Foundation\Application(realpath(__DIR__ . '/../'));

$app->useStoragePath(realpath(__DIR__ . '/../storage/'));

$app->singleton(
    \Illuminate\Contracts\Console\Kernel::class,
    \NewUp\Console\Kernel::class
);

$app->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \NewUp\Exceptions\Handler::class
);

return $app;