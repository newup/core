<?php

use NewUp\Filesystem\Generators\TreeGenerator;

class TreeGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testAddPathsWorks()
    {
        $g = $this->getGenerator();
        $g->addPaths(['key' => ['path' => '', 'type' => '']]);
        $this->assertCount(1, $g->getPaths());
    }

    private function getGenerator()
    {
        $fileSystem = $this->getMock('NewUp\Contracts\Filesystem\Filesystem');
        $generator  = new TreeGenerator($fileSystem);

        return $generator;
    }

    public function testGeneratorIsSortingThePathsInTheCorrectOrder()
    {
        $g = $this->getGenerator();

        $startPaths = [
            'first.php' => ['path' => 'this/should/appear/last/file.php', 'type' => 'file'],
            'second'    => ['path' => 'this/should/appear/first/because/its/nested/further/file.php', 'type' => 'file']
        ];

        $g->addPaths($startPaths);

        // Just test the keys, as the directory separators are normalized.
        $this->assertEquals(['second', 'first.php'], array_keys($g->getPaths()));
    }
}