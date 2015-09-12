<?php

namespace NewUp\Tests;

use NewUp\Configuration\Parsers\YAMLParser;
use NewUp\Tests\Renderer\RenderTestBase;

class YAMLParserTest extends RenderTestBase
{

    private $yamlString = <<<"YAML"
foo: bar
bar:
    foo: bar
    bar: baz

YAML;

    private $expectedValueFromYamlString = [
        'foo' => 'bar',
        'bar' => [
            'foo' => 'bar',
            'bar' => 'baz'
        ]
    ];

    private function getParser()
    {
        return new YAMLParser;
    }

    public function testYAMLParserParsesStrings()
    {
        $p           = $this->getParser();
        $parsedValue = $p->parseString($this->yamlString);
        $this->assertEquals($this->expectedValueFromYamlString, $parsedValue);
    }

    public function testYAMLParserParsesFiles()
    {
        $p           = $this->getParser();
        $parsedValue = $p->parseFile(__DIR__ . '/test.yaml');
        $this->assertEquals($this->expectedValueFromYamlString, $parsedValue);
    }

    public function testYAMLParserReadsNewUpFileNameCollectionsCorrectly()
    {
        $p = $this->getParser();
        $p->trimArrayValues(true);
        $parsedValue = $p->parseFile(__DIR__ . '/fname_test.yaml');

        $this->assertEquals([
            'ServiceProvider.php'  => '{% if (1 == 1) %} hello world {% endif %}',
            'ServiceProvider2.php' => '{{ "test_stuff"|studly }}'
        ], $parsedValue);
    }

    public function testTemplateRenderCanCompileFileNamesFromYaml()
    {
        $r = $this->getRenderer();
        $p = $this->getParser();

        $parsedValue = $p->parseFile(__DIR__ . '/fname_test.yaml');

        $firstValue = $r->renderString($parsedValue['ServiceProvider.php']);
        $this->assertEquals(' hello world ', $firstValue);

        $secondValue = $r->renderString($parsedValue['ServiceProvider2.php']);
        $this->assertEquals('TestStuff', $secondValue);

    }

    public function testParserCreatesYamlFromArray()
    {
        $p = $this->getParser();
        $this->assertEquals(file_get_contents(__DIR__ . '/array_to_yaml.yaml'),
            $p->toYaml($this->expectedValueFromYamlString));
    }

}