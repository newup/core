<?php

namespace NewUp\Templates\Generators;

class AuthorsGenerator
{

    /**
     * Generates a new array of authors where each item is an object.
     *
     * The returned authors array will be in the correct format for exporting to a
     * "composer.json" file.
     *
     * @param array $authors
     * @return array
     */
    public static function generate(array $authors)
    {
        $newAuthors = [];

        foreach ($authors as $author) {
            $newAuthors[] = (object)$author;
        }

        return $newAuthors;
    }

}