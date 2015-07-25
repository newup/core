<?php

namespace NewUp\Templates\Parsers;

use NewUp\Contracts\Templates\PathNameParser;
use NewUp\Contracts\Templates\Renderer;

class FileSystemPathNameParser implements PathNameParser
{

    const ESCAPE_DOUBLE_OPEN_BRACKET = '//ESCAPE_OPEN_SQUARE_BRACKET//';

    /**
     * The Renderer instance.
     *
     * @var \NewUp\Contracts\Templates\Renderer
     */
    protected $templateRenderer;

    /**
     * The characters that should be removed from the final path.
     *
     * @var array
     */
    protected $charactersToRemove = [];

    /**
     * The "standard" environment lexer.
     *
     * @var \Twig_Lexer
     */
    protected $originalTwigLexer = null;

    /**
     * The directory/pathname lexer.
     *
     * @var \Twig_Lexer
     */
    protected $pathLexer = null;

    public function __construct(Renderer $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * Adds characters that should be automatically removed from the final path.
     *
     * @param array $characters
     * @return mixed
     */
    public function addCharactersToRemove(array $characters)
    {
        $this->charactersToRemove = array_merge($this->charactersToRemove, $characters);
    }

    /**
     * Resets the list of characters that should automatically be removed from the final path.
     *
     * @return mixed
     */
    public function resetCharactersToRemove()
    {
        $this->charactersToRemove = [];
    }

    /**
     * Gets the characters that should be removed from the final path.
     *
     * @return mixed
     */
    public function getCharactersToRemove()
    {
        return $this->charactersToRemove;
    }

    /**
     * Removes the "remove" characters from the given string.
     *
     * @param $string
     * @return string
     */
    private function removeUnwantedCharactersFromString($string)
    {
        return str_replace($this->charactersToRemove, '', $string);
    }

    /**
     * Replaces all occurrences of '[[' with an arbitrary escape sequence.
     *
     * @param $string
     * @return mixed
     */
    private function replaceDoubleOpeningBrackets($string)
    {
        return str_replace('[[', self::ESCAPE_DOUBLE_OPEN_BRACKET, $string);
    }

    /**
     * Replaces all occurrences of the opening bracket escape sequence with a single opening square bracket.
     *
     * @param $string
     * @return mixed
     */
    private function replaceBracketEscapeSequenceWithSingleOpeningBrackets($string)
    {
        return str_replace(self::ESCAPE_DOUBLE_OPEN_BRACKET, '[', $string);
    }

    /**
     * Replaces all occurrences of '[' with '|'.
     *
     * @param $string
     * @return mixed
     */
    private function convertSingleOpeningSquareBracketsToPipes($string)
    {
        return str_replace('[', '|', $string);
    }

    /**
     * Makes a new Twig Lexer just for the path name stuff.
     *
     * It should be assumed that 'FileSystemPathNameParser' and 'TemplateRenderer' are friendly classes.
     *
     */
    private function constructPathNameLexer()
    {
        if ($this->originalTwigLexer == null) {
            $this->originalTwigLexer = $this->templateRenderer->getStringEnvironment()->getLexer();
        }

        if ($this->pathLexer == null) {
            $this->pathLexer = new \Twig_Lexer($this->templateRenderer->getStringEnvironment(), [
                'tag_comment'  => ['{#', '#}'],
                'tag_block'    => ['{%', '%}'],
                'tag_variable' => ['{', '}']
            ]);
        }

        // Set the lexer.
        $this->templateRenderer->getStringEnvironment()->setLexer($this->pathLexer);
    }

    /**
     * Restores the original Twig lexer.
     *
     * It should be assumed that 'FileSystemPathNameParser' and 'TemplateRenderer' are friendly classes.
     */
    private function restoreOriginalLexer()
    {
        if ($this->originalTwigLexer != null) {
            $this->templateRenderer->getStringEnvironment()->setLexer($this->originalTwigLexer);
        }
    }

    /**
     * Processes the provided path.
     *
     * @param $path
     * @return mixed
     */
    public function processPath($path)
    {
        $path = $this->replaceDoubleOpeningBrackets($path);
        $path = $this->convertSingleOpeningSquareBracketsToPipes($path);
        $path = $this->replaceBracketEscapeSequenceWithSingleOpeningBrackets($path);
        $this->constructPathNameLexer();
        $path = $this->templateRenderer->renderString($path);
        $this->restoreOriginalLexer();
        $path = $this->removeUnwantedCharactersFromString($path);

        return $path;
    }


}