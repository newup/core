<?php namespace NewUp\Tests\Support;

class HelpersTest extends \PHPUnit_Framework_TestCase
{

    public function testArrayRemoveValueRemovesCorrectValue()
    {
        $array = [
            'test',
            'test2'
        ];

        array_remove_value($array, 'test2');

        $this->assertEquals('test', $array[0]);
    }

}