<?php

namespace NewUp\Configuration;

use Illuminate\Support\Collection;
use NewUp\Configuration\Parsers\YAMLParser;

class ConfigurationWriter extends Collection
{

    public function reset()
    {
        $this->items = [];
    }

    /**
     * Saves the configuration values at the given path.
     *
     * @param $fileName
     */
    public function save($fileName)
    {
        file_put_contents($fileName, $this->toJson(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    public function saveYaml($fileName)
    {
        $yamlParser = new YAMLParser;
        file_put_contents($fileName, $yamlParser->toYaml($this->all()));
    }

}