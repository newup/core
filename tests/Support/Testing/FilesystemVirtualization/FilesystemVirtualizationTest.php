<?php

use NewUp\Support\Testing\FilesystemVirtualization\FilesystemVirtualization;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;

class FilesystemVirtualizationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var VirtualFilesystemStub
     */
    private $virtualSystem;

    public function setUp()
    {
        $this->virtualSystem = new VirtualFilesystemStub;
        $this->virtualSystem->setUpVfs();
    }

    public function tearDown()
    {
        $this->virtualSystem->tearDownVfs();
    }

    public function testGetPathPrependsVirtualPath()
    {
        $this->assertEquals('vfs://virtual/test', $this->virtualSystem->getPath('test'));
        $this->assertEquals('vfs://virtual/hello', $this->virtualSystem->getPath('hello'));
    }

    public function testSetUpMethodCreatesVfs()
    {
        $system = $this->virtualSystem->getVfs();
        $this->assertInstanceOf(vfsStreamDirectory::class, $system);
    }

    public function testTearDownDestroysVfs()
    {
        $this->virtualSystem->tearDownVfs();
        $this->assertNull($this->virtualSystem->getVfs());
    }

    public function testChildCountReturnsCorrectNumber()
    {
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('test'));
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('class'));
        $this->assertEquals(2, $this->virtualSystem->getFileCount());
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('third'));
        $this->assertEquals(3, $this->virtualSystem->getFileCount());
    }

    public function testGetFileAtIndexReturnsCorrectFileForSpecifiedIndex()
    {
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('test'));
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('test2'));
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('test3'));
        $file = $this->virtualSystem->getFileAtIndex(0);
        $this->assertNotNull($file);
        $this->assertEquals('test', $file->getName());

        $file = $this->virtualSystem->getFileAtIndex(2);
        $this->assertNotNull($file);
        $this->assertEquals('test3', $file->getName());

        $file = $this->virtualSystem->getFileAtIndex(1);
        $this->assertNotNull($file);
        $this->assertEquals('test2', $file->getName());
    }

    public function testGetFileByNameReturnsCorrectFile()
    {
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('test'));
        $this->virtualSystem->getVfs()->addChild(vfsStream::newFile('test2'));
        $file = $this->virtualSystem->getFileByName('test2');
        $this->assertNotNull($file);
        $this->assertEquals('test2', $file->getName());
    }

    public function testVirtualizeCreatesContent()
    {
        $this->virtualSystem->virtualize(['test']);
        $this->assertEquals(1, $this->virtualSystem->getFileCount());
        $file = $this->virtualSystem->getFileByName('test');
        $this->assertNotNull($file);
        $this->assertEquals('test', $file->getName());
    }

    public function testVirtualizeCanCreateMultipleFiles()
    {
        $this->virtualSystem->virtualize([
            'test', 'test2', 'test3'
        ]);
        $this->assertEquals(3, $this->virtualSystem->getFileCount());
    }

    public function testVirtualizeCanAcceptVfsContentInstances()
    {
        $this->virtualSystem->virtualize([
            'test',
            vfsStream::newFile('test2'),
            vfsStream::newDirectory('testDir')
        ]);
        $this->assertEquals(3, $this->virtualSystem->getFileCount());
        $this->assertInstanceOf(vfsStreamDirectory::class, $this->virtualSystem->getFileAtIndex(2));
        $this->assertInstanceOf(vfsStreamFile::class, $this->virtualSystem->getFileAtIndex(1));
    }

    public function testVirtualStructureCreatesContent()
    {
        $this->virtualSystem->virtualizeStructure([
            'File.txt',
            'directory' => [
                'nested_file',
                'nested_directory' => [
                    'nested_directory_file'
                ]
            ]
        ]);
        $this->assertEquals(2, $this->virtualSystem->getFileCount());
        $this->assertInstanceOf(vfsStreamFile::class, $this->virtualSystem->getFileAtIndex(0));
        $this->assertInstanceOf(vfsStreamDirectory::class, $this->virtualSystem->getFileAtIndex(1));
    }

    public function testGetContentsReturnsCorrectContents()
    {
        file_put_contents($this->virtualSystem->getPath('test.txt'), 'hi!');
        $this->assertEquals('hi!', $this->virtualSystem->getContents('test.txt'));
    }

}

class VirtualFilesystemStub
{
    use FilesystemVirtualization;

    protected $virtualPath = 'virtual';

}