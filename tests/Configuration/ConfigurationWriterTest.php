<?php

use NewUp\Configuration\ConfigurationWriter;

class ConfigurationWriterTest extends PHPUnit_Framework_TestCase
{

    /**
     * The ConfigurationWriter instance.
     *
     * @var ConfigurationWriter
     */
    private $writer;

    public function setUp()
    {
        $this->writer = new ConfigurationWriter([
            'first' => 'The First',
            'second' => 'The Second',
            'third' => 'The Third'
        ]);
    }

    public function testRestClearsConfigurationItems()
    {
        $this->writer->reset();
        $this->assertEquals(0, $this->writer->count());
    }

    public function testSaveWritesFilesToSystem()
    {

    }

    public function testSaveWritesCorrectJson()
    {

    }

    public function testSaveYamlWritesFilesToSystem()
    {

    }

    public function testSaveYamlWritesCorrectYaml()
    {

    }

}