<?php

use NewUp\Filesystem\Filesystem;
use NewUp\Templates\Loaders\PackageLoader;

class PackageLoaderTest extends \PHPUnit_Framework_TestCase
{

    public function testPackageLoaderGeneratesCorrectNamespaceAndClass()
    {
        $packageLoader = new PackageLoader(new Filesystem());
        $className     = $packageLoader->loadPackage(__DIR__ . '/fixtures/TestPackage');
        $this->assertEquals('Newup\Test\Package', $className);
    }

    public function testPackageLoaderLoadsClasses()
    {
        $packageLoader = new PackageLoader(new Filesystem());
        $className     = $packageLoader->loadPackage(__DIR__ . '/fixtures/TestPackage');
        $package       = new $className;

        $anotherClass = new \Newup\Test\SimplyAClass;
        $nestedClass  = new \Newup\Test\Nested\NestedClass;

        $this->assertInstanceOf('NewUp\Templates\BasePackageTemplate', $package);
        $this->assertInstanceOf('Newup\Test\Package', $package);
        $this->assertInstanceOf('Newup\Test\SimplyAClass', $anotherClass);
        $this->assertInstanceOf('Newup\Test\Nested\NestedClass', $nestedClass);
    }

    public function testLoadedPackageClassMethodsCanBeCalledNormally()
    {
        $packageLoader = new PackageLoader(new Filesystem());
        $className     = $packageLoader->loadPackage(__DIR__ . '/fixtures/TestPackage');
        $package       = new $className;

        $anotherClass = new \Newup\Test\SimplyAClass;
        $nestedClass  = new \Newup\Test\Nested\NestedClass;

        $this->assertEquals("Hello, World!", $package->getPackageName());
        $this->assertEquals("Well, hello there!", $anotherClass->hi());
        $this->assertEquals("Sad to see you go!", $nestedClass->goodbye());
    }

}