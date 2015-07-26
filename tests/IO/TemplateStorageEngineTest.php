<?php

namespace NewUp\Tests\IO;

use NewUp\Filesystem\TemplateStorageEngine;
use NewUp\Templates\Generators\PathNormalizer;

class TemplateStorageEngineTest extends \PHPUnit_Framework_TestCase
{
    use PathNormalizer;

    private function getEngine()
    {
        $files = $this->getMock('NewUp\Contracts\Filesystem\Filesystem');
        return new TemplateStorageEngine($files, template_storage_path());
    }

    private function getPath()
    {
        return $this->normalizePath(template_storage_path());
    }

    public function testEngineReturnsCorrectStoragePath()
    {
        $engine = $this->getEngine();
        $this->assertEquals($this->getPath(), $engine->getStoragePath());
    }

}