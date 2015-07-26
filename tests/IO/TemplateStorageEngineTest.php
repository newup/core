<?php

namespace NewUp\Tests\IO;

use NewUp\Exceptions\InvalidArgumentException;
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

    public function testEngineParsesVersionCorrectly()
    {
        $engine = $this->getEngine();
        $this->assertEquals('2.4.2', $engine->getPackageVersion('test/test:2.4.2'));
        $this->assertEquals('4.4.2', $engine->getPackageVersion('test/test:4.4.2'));
        $this->assertEquals(null, $engine->getPackageVersion('test/test'));
        $this->assertEquals(null, $engine->getPackageVersion('test/test:'));
    }

    /**
     * @expectedException NewUp\Exceptions\InvalidArgumentException
     */
    public function testEngineThrowsErrorOnInvalidVersionString()
    {
        $engine = $this->getEngine();
        $engine->getPackageVersion('test/test:2.4.2:bad');
    }

    public function testEngineParsesPackageNameWithoutVersion()
    {
        $engine = $this->getEngine();
        $reflectionEngine = new \ReflectionClass(get_class($engine));
        $method = $reflectionEngine->getMethod('getPackageWithoutVersionString');
        $method->setAccessible(true);

        $this->assertEquals('test/test', $method->invokeArgs($engine, ['test/test']));
        $this->assertEquals('test/test', $method->invokeArgs($engine, ['test/test:23.23']));
        $this->assertEquals('test/test', $method->invokeArgs($engine, ['test/test:4.2.1']));
    }

    /**
     * @expectedException NewUp\Exceptions\InvalidArgumentException
     */
    public function testEngineParsesPackageNameWithoutVersionThrowsExceptionForEmptyPackageString()
    {
        $engine = $this->getEngine();
        $reflectionEngine = new \ReflectionClass(get_class($engine));
        $method = $reflectionEngine->getMethod('getPackageWithoutVersionString');
        $method->setAccessible(true);

        $method->invokeArgs($engine, ['']);
    }

}