<?php

use NewUp\Templates\Package;

class PackageTest extends PHPUnit_Framework_TestCase {

    private function getPackageDetails()
    {
        return [
          'name' => 'vendor/package',
          'description' => 'Test description',
          'license' => 'MIT',
          'authors' => [
              ['name' => 'User', 'email' => 'email@example.com'],
              ['name' => 'Another user', 'email' => 'user2@example.com']
          ]
        ];
    }

    private function getPackageFromDetails()
    {
        return Package::fromDetails($this->getPackageDetails());
    }

    public function testPackageLoadsFromDetails()
    {
        $package = $this->getPackageFromDetails();
        $this->assertInstanceOf('NewUp\Contracts\Packages\Package', $package);
    }

    public function testPackageLoadsFromFile()
    {
        $package = Package::fromFile(__DIR__.'/stubs/composer.json');
        $this->assertInstanceOf('NewUp\Contracts\Packages\Package', $package);
    }

    public function testPackageFromFileSetsDataCorrectly()
    {
        $package = Package::fromFile(__DIR__.'/stubs/composer.json');
        $this->assertEquals($package->getVendor(), 'vendor');
        $this->assertEquals($package->getPackage(), 'package');
        $this->assertEquals($package->getDescription(), 'An example');
        $this->assertEquals($package->getLicense(), 'MIT');
        $this->assertCount(1, $package->getAuthors());
    }

    public function testPackageSetsDataCorrectly()
    {
        $package = $this->getPackageFromDetails();
        $this->assertEquals($package->getVendor(), 'vendor');
        $this->assertEquals($package->getPackage(), 'package');
        $this->assertEquals($package->getDescription(), $this->getPackageDetails()['description']);
        $this->assertEquals($package->getLicense(), $this->getPackageDetails()['license']);
        $this->assertCount(count($this->getPackageDetails()['authors']), $package->getAuthors());
    }

    public function testPackageGetNameReturnsCorrectFormat()
    {
        $package = $this->getPackageFromDetails();
        $this->assertEquals('vendor/package', $package->getName());
    }

    public function testPackageConvertsAuthorsIntoObjects()
    {
        $package = $this->getPackageFromDetails();
        $packageAuthors = $package->getAuthors();
        $authors = $this->getPackageDetails()['authors'];

        for ($i = 0; $i < count($authors); $i++)
        {
            $this->assertInstanceOf('stdClass', $packageAuthors[$i]);
            $this->assertEquals($packageAuthors[$i], (object)$authors[$i]);
        }

    }

    public function testPackageExportsCorrectJSONStructure()
    {
        $package = $this->getPackageFromDetails();
        $this->assertEquals($package, Package::fromDetails(json_decode($package->toJson(), true)));
    }

}