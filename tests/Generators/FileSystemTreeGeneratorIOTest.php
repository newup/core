<?php

namespace NewUp\Tests\Generators;

use NewUp\Filesystem\Filesystem;
use NewUp\Templates\Generators\FileSystemTreeGenerator;
use NewUp\Templates\Generators\PathNormalizer;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FileSystemTreeGeneratorIOTest extends \PHPUnit_Framework_TestCase
{

    use PathNormalizer;

    /**
     * The vfsStreamDirectory instance.
     *
     * @var vfsStreamDirectory
     */
    private $vfs;

    protected function setUp()
    {
        $this->vfs = vfsStream::setup('fst');
    }

    private function getGenerator()
    {
        $fileSystem = new Filesystem;
        $generator  = new FileSystemTreeGenerator($fileSystem);

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

        $g->generate(vfsStream::url('fst'));

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
            $this->assertTrue($this->vfs->hasChild($child));
        }

    }

    public function testGeneratorIgnoresSpecificFiles()
    {
        $g = $this->getGenerator();

        $g->addIgnoredPath('*.gitignore');
        $g->generate(vfsStream::url('fst'));

        $this->assertFalse($this->vfs->hasChild('.gitignore'));
    }

    public function testGeneratorRemovesSpecificFiles()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('*.gitignore');
        $g->generate(vfsStream::url('fst'));
        $this->assertFalse($this->vfs->hasChild('.gitignore'));
    }

    public function testGeneratorPreservesDirectoryStructureWhenRemovingNestedFiles()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('*nested\file.txt');
        $g->generate(vfsStream::url('fst'));
        $this->assertFalse($this->vfs->hasChild('some/nested/file.txt'));
        $this->assertTrue($this->vfs->hasChild('some/nested'));
    }

    public function testGeneratorIgnoresWithWildCard()
    {
        $g = $this->getGenerator();
        $g->addIgnoredPath('*some/*');
        $g->generate(vfsStream::url('fst'));
        $this->assertCount(2, $this->vfs->getChildren());

        foreach ([
                     'some',
                     'some/file.txt',
                     'some/nested',
                     'some/dir',
                     'some/nested/file.txt'
                 ] as $path) {
            $this->assertFalse($this->vfs->hasChild($path));
        }

        foreach ([
                     '.gitignore',
                     'root.txt'
                 ] as $path) {
            $this->assertTrue($this->vfs->hasChild($path));
        }

    }

    public function testGeneratorRemovesWithWildCard()
    {
        $g = $this->getGenerator();
        $g->addAutomaticallyRemovedPath('*nested*');
        $g->generate(vfsStream::url('fst'));
        $this->assertFalse($this->vfs->hasChild('some/nested/file.txt'));
        $this->assertFalse($this->vfs->hasChild('some/nested'));
    }

    public function testFileSystemTreeGeneratorReturnsAnArrayOfTheFilesCreated()
    {
        $g     = $this->getGenerator();
        $paths = $g->generate(vfsStream::url('fst'));

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