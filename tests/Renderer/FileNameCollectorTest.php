<?php

namespace NewUp\Tests\Renderer;

use NewUp\Templates\Parsers\YAMLParser;
use NewUp\Templates\Renderers\Collectors\FileNameCollector;

class FileNameCollectorTest extends RenderTestBase
{

    private function getCollector()
    {
        $c = new FileNameCollector;

        return $c;
    }

    private function getYamlParser()
    {
        return new YAMLParser;
    }

    public function testFileNameCollectorReturnsEmptyArrayWithoutAnyFileNames()
    {
        $c         = $this->getCollector();
        $pathNames = $c->collect();

        $this->assertInternalType('array', $pathNames);
        $this->assertCount(1, $pathNames);
        $this->assertArrayHasKey('sys_pathNames', $pathNames);
    }

    public function testFileNameCollectorBuildsTheCorrectArray()
    {
        $c = $this->getCollector();
        $p = $this->getYamlParser();

        $c->addFileNames($p->parseFile(__DIR__ . '/../YAML/fname_test.yaml'));
        $c->addFileNames(['sample' => 'filename']);
        $pathNames = $c->collect();

        $this->assertInternalType('array', $pathNames);
        $this->assertCount(1, $pathNames);
        $this->assertArrayHasKey('sys_pathNames', $pathNames);

        $actualPathNames = $pathNames['sys_pathNames'];
        $this->assertCount(3, $actualPathNames);
        $this->assertArrayHasKey('sample', $actualPathNames);
        $this->assertArrayHasKey('ServiceProvider.php', $actualPathNames);
        $this->assertArrayHasKey('ServiceProvider2.php', $actualPathNames);
    }

}