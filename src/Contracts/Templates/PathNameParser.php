<?php

namespace NewUp\Contracts\Templates;

interface PathNameParser
{

    /**
     * Adds characters that should be automatically removed from the final path.
     *
     * @param array $characters
     * @return mixed
     */
    public function addCharactersToRemove(array $characters);

    /**
     * Resets the list of characters that should automatically be removed from the final path.
     *
     * @return mixed
     */
    public function resetCharactersToRemove();

    /**
     * Gets the characters that should be removed from the final path.
     *
     * @return mixed
     */
    public function getCharactersToRemove();

    /**
     * Processes the provided path.
     *
     * @param $path
     * @return mixed
     */
    public function processPath($path);

}