<?php

use NewUp\Filesystem\Filesystem;
use NewUp\Filesystem\DirectoryAnalyzer;
use NewUp\Support\Testing\FilesystemVirtualization\FilesystemVirtualization;

class DirectoryAnalyzerTest extends \PHPUnit_Framework_TestCase
{

    use FilesystemVirtualization;

    protected $virtualPath = 'fst';

    protected function setUp()
    {
        $this->setUpVfs();
        $this->virtualize([
            'test/directory/dir/newup.keep',
            'test/directory/first.php',
            'test/file.txt',
            'first.php'
        ]);
    }

    public function tearDown()
    {
        $this->tearDownVfs();
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
        $a = $this->getAnalyzer();
        $actualArray = $a->analyze($this->getPath(''));
        $this->assertCount(4, $actualArray);

        // All of the entries should have the 'file' type.
        foreach ($actualArray as $entry) {
            $this->assertEquals('file', $entry['type']);
        }
    }

}