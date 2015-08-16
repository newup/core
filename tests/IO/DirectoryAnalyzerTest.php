<?php

namespace NewUp\Contracts\IO;

use NewUp\Filesystem\Filesystem;
use NewUp\Templates\Analyzers\DirectoryAnalyzer;
use org\bovigo\vfs\vfsStream;

class DirectoryAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The vfsStreamDirectory instance.
     *
     * @var vfsStreamDirectory
     */
    private $vfs;

    /**
     * Sets up the vfsStream instance and initializes the test directory structure.
     *
     * The following directory structure will be utilized in tests:
     *
     * test/directory/dir/newup.keep
     * test/directory/first.php
     * test/file.txt
     * first.php
     *
     */
    protected function setUp()
    {
        $this->vfs = vfsStream::setup('fst');
        $this->vfs->addChild(vfsStream::newFile('test/directory/dir/newup.keep'));
        $this->vfs->addChild(vfsStream::newFile('test/directory/first.php'));
        $this->vfs->addChild(vfsStream::newFile('test/file.txt'));
        $this->vfs->addChild(vfsStream::newFile('first.php'));
    }

    protected function getAnalyzer()
    {
        $fs = new Filesystem;

        return new DirectoryAnalyzer($fs);
    }

    /**
     * @expectedException NewUp\Exceptions\InvalidPathException
     */
    public function testDirectoryAnalyzerThrowsExceptionOnInvalidPath()
    {
        $this->getAnalyzer()->analyze('|');
    }

    public function testDirectoryAnalyzerReturnsTheCorrectArray()
    {
        $a           = $this->getAnalyzer();
        $actualArray = $a->analyze(vfsStream::url('fst'));
        $this->assertCount(4, $actualArray);

        // All of the entries should have the 'file' type.
        foreach ($actualArray as $entry)
        {
            $this->assertEquals('file', $entry['type']);
        }
    }

}