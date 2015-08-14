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
        $composer = $this->getMockBuilder('NewUp\Foundation\Composer\Composer')->disableOriginalConstructor()->getMock();
        return new TemplateStorageEngine($files, $composer, template_storage_path());
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
        $this->assertEquals('2.4.2', $engine->getPackageVersion('test/test:2.4.2>--prefer-source'));
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
        $engine           = $this->getEngine();
        $reflectionEngine = new \ReflectionClass(get_class($engine));
        $method           = $reflectionEngine->getMethod('getPackageWithoutVersionString');
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
        $engine           = $this->getEngine();
        $reflectionEngine = new \ReflectionClass(get_class($engine));
        $method           = $reflectionEngine->getMethod('getPackageWithoutVersionString');
        $method->setAccessible(true);

        $method->invokeArgs($engine, ['']);
    }

    public function testEngineResolvesCorrectPackagePaths()
    {
        $engine = $this->getEngine();

        $paths = [
            'test/package'     => $this->normalizePath(template_storage_path() . 'test/package/', true),
            'test/package:2.3' => $this->normalizePath(template_storage_path() . 'test/package/2.3/', true),
            'test/package:6.3' => $this->normalizePath(template_storage_path() . 'test/package/6.3/', true),
            'newup/package'    => $this->normalizePath(template_storage_path() . 'newup/package/', true),
            'newup/test:32'    => $this->normalizePath(template_storage_path() . 'newup/test/32/', true),
            'newup/test:dev'   => $this->normalizePath(template_storage_path() . 'newup/test/dev/', true),
        ];

        foreach ($paths as $packageName => $expectedPath) {
            $this->assertEquals($expectedPath, $engine->resolvePackagePath($packageName));
        }
    }

}