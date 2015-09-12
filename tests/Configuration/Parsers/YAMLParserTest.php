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

    public function testYAMLParserParsesStrings()
    {
        $p = $this->getYAMLParser();
        $yamlString = loadFixtureContent('Configuration/Parsers/yaml_string.yaml');
        $parsedValue = $p->parseString($yamlString);
        $this->assertEquals($this->expectedValueFromYAMLString, $parsedValue);
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
            'ServiceProvider.php'  => '{% if (1 == 1) %} hello world {% endif %}',
            'ServiceProvider2.php' => '{{ "test_stuff"|studly }}'
        ], $parsedValue);
    }

}