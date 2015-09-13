<?php

use NewUp\Configuration\Parsers\YAMLParser;

class YAMLParserTest extends PHPUnit_Framework_TestCase
{

    private $expectedValueFromYAMLString = [
        'foo' => 'bar',
        'bar' => [
            'foo' => 'bar',
            'bar' => 'baz'
        ]
    ];

    private function getYAMLParser()
    {
        return new YAMLParser;
    }

    public function testSettingsTrimValuesWorksCorrectly()
    {
        $p = $this->getYAMLParser();
        $this->assertEquals(false, $p->willTrimArrayValues());
        $p->trimArrayValues();
        $this->assertEquals(true, $p->willTrimArrayValues());
        $p->trimArrayValues(false);
        $this->assertEquals(false, $p->willTrimArrayValues());
    }

    public function testParserTrimsArraysCorrectly()
    {
        $p = $this->getYAMLParser();
        $p->trimArrayValues();
        $value = $p->parseString(loadFixtureContent('Configuration/Parsers/yaml_trimmable.yaml'));

        $this->assertEquals([
            'first' => 'first second third',
            'second' => 'first second third'
        ], $value);
    }

    public function testYAMLParserParsesStrings()
    {
        $p = $this->getYAMLParser();
        $yamlString = loadFixtureContent('Configuration/Parsers/yaml_string.yaml');
        $parsedValue = $p->parseString($yamlString);
        $this->assertEquals($this->expectedValueFromYAMLString, $parsedValue);
    }

    public function testYAMLParserCanParseSomethingTwiceInARow()
    {
        $p = $this->getYAMLParser();
        $yamlString = loadFixtureContent('Configuration/Parsers/yaml_string.yaml');
        $parsedValue = $p->parseString($yamlString);
        $this->assertEquals($parsedValue, $p->parseFile(getFixturePath('Configuration/Parsers/yaml_string.yaml')));
    }

    public function testYAMLParserDoesNotRetainStateBetweenParses()
    {
        $p = $this->getYAMLParser();
        $firstValue = $p->parseFile(getFixturePath('Configuration/Parsers/yaml_string.yaml'));
        $secondValue = $p->parseFile(getFixturePath('Configuration/Parsers/yaml_trimmable.yaml'));
        $this->assertNotEquals($firstValue, $secondValue);
    }

    public function testYAMLParserParsesFiles()
    {
        $p = $this->getYAMLParser();
        $parsedValue = $p->parseFile(getFixturePath('Configuration/Parsers/yaml_string.yaml'));
        $this->assertEquals($this->expectedValueFromYAMLString, $parsedValue);
    }

    public function testParserCreatesYAMLFromArray()
    {
        $p = $this->getYAMLParser();
        $this->assertEquals(loadFixtureContent('Configuration/Parsers/yaml_string.yaml'),
            $p->toYaml($this->expectedValueFromYAMLString));
    }

    public function testYAMLParserReadsNewUpFileNameCollectionsCorrectly()
    {
        $p = $this->getYAMLParser();
        $p->trimArrayValues(true);
        $parsedValue = $p->parseFile(getFixturePath('Configuration/Parsers/yaml_filenames.yaml'));

        $this->assertEquals([
            'ServiceProvider.php' => '{% if (1 == 1) %} hello world {% endif %}',
            'ServiceProvider2.php' => '{{ "test_stuff"|studly }}'
        ], $parsedValue);
    }

}