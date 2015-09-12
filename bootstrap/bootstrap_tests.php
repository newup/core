<?php

include 'autoload.php';

include 'app.php';

if (!function_exists('loadFixtureContent')) {
    /**
     * Returns the content of a given fixture file.
     *
     * @param $file
     * @return string
     */
    function loadFixtureContent($file) {
        return file_get_contents(realpath(__DIR__.'/../tests/fixtures/'.$file));
    }
}

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();