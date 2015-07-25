<?php

namespace NewUp\Tests\Generators;

use NewUp\Templates\Generators\AuthorsGenerator;

class AuthorsGeneratorTest extends \PHPUnit_Framework_TestCase
{

    protected $authors = [
        ['name' => 'Johnathon Koster', 'email' => 'john@stillat.com'],
        ['name' => 'Example', 'email' => 'user@example.com']
    ];

    public function testGeneratorConvertsNestedArraysToObjects()
    {
        $authors = AuthorsGenerator::generate($this->authors);

        foreach ($authors as $author) {
            $this->assertInstanceOf('\stdClass', $author);
        }
    }


}