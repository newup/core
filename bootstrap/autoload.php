<?php

if (!defined('NEWUP_LOADER_PATH')) {
    define('NEWUP_LOADER_PATH', __DIR__ . '/../vendor/autoload.php');
}

$loader = require NEWUP_LOADER_PATH;

return $loader;