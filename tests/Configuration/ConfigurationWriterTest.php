<?php

namespace NewUp\Tests\Configuration;

use NewUp\Configuration\ConfigurationWriter;
use NewUp\Templates\Parsers\YAMLParser;

class ConfigurationWriterTest extends \PHPUnit_Framework_TestCase
{

    protected $defaultConfigurationItems = [
        'first'  => 'first-value',
        'second' => 'second-value'
    ];

    public function testFirstReturnsFirstItemInCollection()
    {
        $w = new ConfigurationWriter($this->defaultConfigurationItems);
        $this->assertEquals('first-value', $w->first());
    }

    public function testWriterReturnsCorrectJson()
    {
        $w = new ConfigurationWriter($this->defaultConfigurationItems);
        $this->assertEquals(json_encode($this->defaultConfigurationItems), $w->toJson());
    }

    public function testWriterSavesJson()
    {
        $w = new ConfigurationWriter($this->defaultConfigurationItems);
        $w->save(__DIR__ . '/test/test.json');

        $this->assertFileExists(__DIR__ . '/test/test.json');
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/test/test_expected.json')),
            json_decode(file_get_contents(__DIR__ . '/test/test.json')));
        @unlink(__DIR__ . '/test/test.json');
    }

    public function testWriterSavesYaml()
    {
        $w = new ConfigurationWriter($this->defaultConfigurationItems);
        $w->saveYaml(__DIR__ . '/test/test.yaml');

        $yamlParser = new YAMLParser;

        $this->assertFileExists(__DIR__ . '/test/test.yaml');
        $this->assertEquals($yamlParser->parseFile(__DIR__ . '/test/test_expected.yaml'),
            $yamlParser->parseFile(__DIR__ . '/test/test.yaml'));
        @unlink(__DIR__ . '/test/test.yaml');
    }

}