<?php

namespace NewUp\Tests\Generators;

use NewUp\Filesystem\Generators\PathTreeArrayFormat;

class PathTreeArrayFormatTest extends \PHPUnit_Framework_TestCase
{

    public function testPathTreeArrayTraitDoesNotModifyValidArrays()
    {
        $pathTreeArrayFormatter = new PathTreeArrayFormatProxy;

        $validValue = [
            'path' => 'test/test.php',
            'type' => 'file'
        ];

        $value = $pathTreeArrayFormatter->p_getPathForTreeGenerator('test.php', $validValue);
        $this->assertEquals($validValue, $value);
    }

    /**
     * @expectedException \NewUp\Exceptions\InvalidArgumentException
     */
    public function testPathTreeArrayTraitThrowsExceptionWithInvalidKeys()
    {
        $pathTreeArrayFormatter = new PathTreeArrayFormatProxy;
        $pathTreeArrayFormatter->p_getPathForTreeGenerator('test.php', 'test.php');
    }

    public function testPathTreeArrayTraitCanProcessPathKeyOptions()
    {
        $pathTreeArrayFormatter = new PathTreeArrayFormatProxy;

        $this->assertEquals([
            'path' => 'test/path.php',
            'type' => 'file'
        ], $pathTreeArrayFormatter->p_getPathForTreeGenerator('path.php]f', 'test/path.php'));
    }

}

class PathTreeArrayFormatProxy
{

    use PathTreeArrayFormat;

    public function p_getPathForTreeGenerator($pathKey, $path)
    {
        return $this->getPathForTreeGenerator($pathKey, $path);
    }

}