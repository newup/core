<?php

use NewUp\Templates\Parsers\FileSystemPathNameParser;
use NewUp\Templates\Renderers\TemplateRenderer;

class FileSystemPathNameParserTest extends \PHPUnit_Framework_TestCase
{

    protected $renderer = null;

    /**
     * @return TemplateRenderer
     */
    private function getRenderer()
    {
        if ($this->renderer == null) {
            $this->renderer = new TemplateRenderer();
        }

        return $this->renderer;
    }

    /**
     * @return \NewUp\Contracts\Templates\PathNameParser
     */
    private function getParser()
    {
        return new FileSystemPathNameParser($this->getRenderer());
    }

    protected function tearDown()
    {
        $this->renderer = null;
    }

    public function testAddingNewCharactersMergesCharacterArray()
    {
        $p = $this->getParser();
        $p->addCharactersToRemove(['\\']);
        $this->assertEquals(['\\'], $p->getCharactersToRemove());
        $p->addCharactersToRemove(['/']);
        $this->assertEquals(['\\', '/'], $p->getCharactersToRemove());
    }

    public function testResettingCharactersClearsCharacterArray()
    {
        $p = $this->getParser();
        $p->addCharactersToRemove(['a', 'b']);
        $p->resetCharactersToRemove();
        $this->assertEmpty($p->getCharactersToRemove());
    }

    public function testParserDoesNotAffectNonSpecialStrings()
    {
        $p = $this->getParser();
        $this->assertEquals('not special', $p->processPath('not special'));
    }

    public function testParserRemovesCharactersToRemove()
    {
        $p = $this->getParser();
        $p->addCharactersToRemove([' ']);
        $this->assertEquals('somestring', $p->processPath('some string'));
    }

    public function testParserRemovesMultipleCharactersToRemove()
    {
        $p = $this->getParser();
        $p->addCharactersToRemove([' ', '/']);
        $this->assertEmpty($p->processPath('  //'));
    }

    public function testOpeningSquareBracketsAreEscapedCorrectly()
    {
        $p = $this->getParser();
        $this->assertEquals('[', $p->processPath('[['));
    }

    public function testSingleOpeningBracketsAreConvertedToPipes()
    {
        $p = $this->getParser();
        $this->assertEquals('|', $p->processPath('['));
    }

    public function testLiterals()
    {
        $p = $this->getParser();
        $this->assertEquals("literal", $p->processPath('{ "literal" }'));
    }

    public function testFilters()
    {
        $p = $this->getParser();
        $this->assertEquals('HI', $p->processPath('{ "hi"[upper }'));
        $this->assertEquals('hi', $p->processPath('{ "HI"[lower }'));
        $this->assertEquals('this-is-a-test', $p->processPath('{ "this is a test"[slug(\'-\') }'));
    }

    public function testOpenSquareBracketsAreUnEscaped()
    {
        $p = $this->getParser();
        $this->assertEquals('test[', $p->processPath('{ "test" }[['));
    }

    public function testMultipleVariableTags()
    {
        $p = $this->getParser();
        $this->assertEquals('hello there', $p->processPath('{ "hello" } { "there" }'));
    }

    public function testRenderGetsData()
    {
        $p = $this->getParser();
        $r = $this->getRenderer();
        $r->setData('test', 'this is a test');
        $this->assertEquals('this-is-a-test', $p->processPath('{ test|slug }'));
    }

}