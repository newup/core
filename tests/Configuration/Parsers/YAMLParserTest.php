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

    }

    public function testParserCreatesYAMLFromArray()
    {

    }

}