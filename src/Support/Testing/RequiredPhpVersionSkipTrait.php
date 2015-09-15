<?php

namespace NewUp\Support\Testing;

use PHPUnit_Framework_Assert as PHPUnit;

trait RequiredPhpVersionSkipTrait
{

    public function requirePhpVersion($operator, $version)
    {
        if (!version_compare(PHP_VERSION, $version, $operator)) {
            PHPUnit::markTestSkipped('Test requires PHP version '.$operator.' '.$version);
        }
    }

}