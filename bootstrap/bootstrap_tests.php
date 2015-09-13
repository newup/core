<?php

include 'autoload.php';

include 'app.php';

// Include the test helper functions.
include __DIR__.'/../src/Support/Testing/helpers.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();