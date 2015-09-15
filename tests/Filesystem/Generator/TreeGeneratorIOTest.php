<?php

use NewUp\Filesystem\Filesystem;
use NewUp\Filesystem\Generators\TreeGenerator;
use NewUp\Filesystem\PathNormalizer;
use NewUp\Support\Testing\FilesystemVirtualization\FilesystemVirtualization;
use NewUp\Support\Testing\FilesystemVirtualization\AssertionsTrait;

class TreeGeneratorIOTest extends \PHPUnit_Framework_TestCase
{

    use PathNormalizer, FilesystemVirtualization, AssertionsTrait {
        FilesystemVirtualization::getPath insteadof AssertionsTrait;
    }

    protected $virtualPath = 'fst';

    protected function setUp()
    {
        $this->setUpVfs();
    }

    protected function tearDown()
    {
        $this->tearDownVfs();
    }

    private function getGenerator()
    {
        $fileSystem = new Filesystem;
        $generator  = new TreeGenerator($fileSystem);

        $generator->addPaths([
            'someKey'    => ['path' => 'some/file.txt', 'type' => 'file', 'home' => 'some/'],
            'anotherKey' => ['path' => 'some/nested/file.txt', 'type' => 'file', 'home' => 'some/nested/'],
            'thirdKey'   => ['path' => 'some/dir', 'type' => 'dir', 'home' => 'some/'],
            'fourthKey'  => ['path' => 'root.txt', 'type' => 'file', 'home' => '/'],
            'ignore'     => ['path' => '.gitignore', 'type' => 'file', 'home' => '/'],
        ]);

        return $generator;
    }

    public function testFileSystemTreeGeneratorCanCreateFilesAndDirectories()
    {
        $g = $this->getGenerator();

        $g->generate($this->getPath());

        // These are the directories and files that should be created.
        $testChildren = [
            'some',
            'some/file.txt',
            'some/nested',
            'some/dir',
            'some/nested/file.txt',
            '.gitignore',
            'root.txt'
        ];

        foreach ($testChildren as $child) {
            $this->assertVfsHasChild($child);
        }

    }

    public function testGeneratorIgnoresSpecificFiles()
    {
        $g = $this->getGenerator();

        $g->addIgnoredPath('*.gitignore');
        $g->generate($this->getPath());

        $this->assertVfsDoesNotHaveChild('.gitignore');
    }

    public function testGeneratorRemovesSpecificFiles()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('*.gitignore');
        $g->generate($this->getPath());
        $this->assertVfsDoesNotHaveChild('.gitignore');
    }

    public function testGeneratorPreservesDirectoryStructureWhenRemovingNestedFiles()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('*nested\file.txt');
        $g->generate($this->getPath());
        $this->assertVfsDoesNotHaveChild('some/nested/file.txt');
        $this->assertVfsHasChild('some/nested');
    }

    public function testGeneratorIgnoresWithWildCard()
    {
        $g = $this->getGenerator();
        $g->addIgnoredPath('*some/*');
        $g->generate($this->getPath());
        $this->assertEquals(2, $this->getFileCount());

        $this->assertVfsDoesNotHaveChild([
            'some',
            'some/file.txt',
            'some/nested',
            'some/dir',
            'some/nested/file.txt'
        ]);

        $this->assertVfsHasChild([
           '.gitignore',
            'root.txt'
        ]);

    }

    public function testGeneratorRemovesWithWildCard()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('*nested*');
        $g->generate($this->getPath());

        $this->assertVfsDoesNotHaveChild([
           'some/nested/file.txt',
            'some/nested'
        ]);
    }

    public function testFileSystemTreeGeneratorReturnsAnArrayOfTheFilesCreated()
    {
        $g     = $this->getGenerator();
        $paths = $g->generate($this->getPath());

        $this->assertCount(5, $paths);

        foreach (['ignore', 'fourthKey', 'thirdKey', 'anotherKey', 'someKey'] as $key) {
            $this->assertArrayHasKey($key, $paths);
        }

        foreach ($paths as $path) {
            foreach (['path', 'type', 'full'] as $pathPart) {
                $this->assertArrayHasKey($pathPart, $path);
            }
        }

    }

    public function testAddIgnoredFilesWorks()
    {
        $g = $this->getGenerator();
        $g->addIgnoredPath('test');
        $g->addIgnoredPath('test2');

        $this->assertCount(2, $g->getIgnoredPaths());
    }

    public function testAddAutomaticallyRemovedPathsWorks()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('test');
        $g->addAutomaticallyRemovedPath('test2');

        $this->assertCount(2, $g->getAutomaticallyRemovedPaths());
    }

    public function testResetIgnoredPathsWorks()
    {
        $g = $this->getGenerator();
        $g->addIgnoredPath('test');
        $g->resetIgnoredPaths();


        $this->assertCount(0, $g->getIgnoredPaths());
    }

    public function testResetAutomaticallyRemovedPathsWorks()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('test');
        $g->resetAutomaticallyRemovedPaths();

        $this->assertCount(0, $g->getAutomaticallyRemovedPaths());
    }

    public function testRemoveSpecificIgnoredPathWorks()
    {
        $g = $this->getGenerator();
        $g->addIgnoredPath('test');
        $g->removeIgnoredPath('test');

        $this->assertCount(0, $g->getIgnoredPaths());

        $g->addIgnoredPath('test');
        $g->addIgnoredPath('test2');
        $g->removeIgnoredPath('test');

        $this->assertEquals('test2', $g->getIgnoredPaths()[0]);
    }

    public function testRemoveSpecificAutomaticallyRemovedPathWorks()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('test');
        $g->removeAutomaticallyRemovedPath('test');

        $this->assertCount(0, $g->getAutomaticallyRemovedPaths());

        $g->addAutomaticallyRemovedPath('test');
        $g->addAutomaticallyRemovedPath('test2');
        $g->removeAutomaticallyRemovedPath('test');

        $this->assertEquals('test2', $g->getAutomaticallyRemovedPaths()[0]);
    }


}