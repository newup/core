<?php

namespace NewUp\Templates\Parsers;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class YAMLParser
{

    /**
     * The parser instance.
     *
     * @var Parser
     */
    protected $yamlParser = null;

    /**
     * Indicates if array values (strings) will be trimmed.
     *
     * @var bool
     */
    protected $trimArrayValues = false;

    /**
     * Gets a Symfony YAML parser.
     *
     * @return Parser
     */
    private function getParser()
    {
        if ($this->yamlParser == null) {
            $this->yamlParser = new Parser;
        }

        return $this->yamlParser;
    }

    /**
     * Parses a YAML file.
     *
     * @param  $fileLocation
     * @return string
     */
    public function parseFile($fileLocation)
    {
        return $this->parseString(file_get_contents($fileLocation));
    }

    /**
     * Recursively trims all string values in an array.
     *
     * @param  $array
     * @return array
     */
    private function trimArray(&$array)
    {
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $array[$key] = trim($value);
            } else if (is_array($value)) {
                $array[$key] = $this->trimArray($value);
            }
        }

        return $array;
    }

    /**
     * Sets whether or not the parser trims array values.
     *
     * @param bool $value
     */
    public function trimArrayValues($value)
    {
        $this->trimArrayValues = $value;
    }

    /**
     * Parses a YAML string.
     *
     * @param  $string
     * @return string
     */
    public function parseString($string)
    {
        $value = $this->getParser()->parse($string);

        if ($this->trimArrayValues && is_array($value)) {
            return $this->trimArray($value);
        }

        return $value;
    }

    /**
     * Converts a PHP value to YAML.
     *
     * @param mixed $input                  The PHP value
     * @param int   $inline                 The level where you switch to inline YAML
     * @param int   $indent                 The level of indentation (used internally)
     * @param bool  $exceptionOnInvalidType true if an exception must be thrown on invalid types (a PHP resource or
     *                                      object), false otherwise
     * @param bool  $objectSupport          true if object support is enabled, false otherwise
     *
     * @return string The YAML representation of the PHP value
     */
    public function toYaml($input, $inline = 2, $indent = 4, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        return Yaml::dump($input, $inline, $indent, $exceptionOnInvalidType, $objectSupport);
    }

}