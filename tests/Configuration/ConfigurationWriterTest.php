<?php

use NewUp\Configuration\ConfigurationWriter;
use NewUp\Support\Testing\FilesystemVirtualization\FilesystemVirtualization;

class ConfigurationWriterTest extends PHPUnit_Framework_TestCase
{

    use FilesystemVirtualization;

    /**
     * The ConfigurationWriter instance.
     *
     * @var ConfigurationWriter
     */
    private $writer;

    protected $virtualPath = 'config';

    public function setUp()
    {
        $this->writer = new ConfigurationWriter([
            'first' => 'The First',
            'second' => 'The Second',
            'third' => 'The Third'
        ]);
        $this->setUpVfs();
    }

    public function tearDown()
    {
        $this->tearDownVfs();
    }

    public function testRestClearsConfigurationItems()
    {
        $this->writer->reset();
        $this->assertEquals(0, $this->writer->count());
    }

    public function testSaveWritesFilesToSystem()
    {
        $this->writer->save($this->getPath('test.json'));
        $this->assertFileExists($this->getPath('test.json'));
    }

    public function testSaveWritesCorrectJson()
    {
        $this->writer->save($this->getPath('test.json'));
        $this->assertEquals(loadFixtureContent('Configuration/expected.json'), $this->getContents('test.json'));
    }

    public function testSaveYamlWritesFilesToSystem()
    {
        $this->writer->saveYaml($this->getPath('test.yaml'));
        $this->assertFileExists($this->getPath('test.yaml'));
    }

    public function testSaveYamlWritesCorrectYaml()
    {
        $this->writer->saveYaml($this->getPath('test.yaml'));
        $this->assertEquals(loadFixtureContent('Configuration/expected.yaml'), $this->getContents('test.yaml'));
    }

}