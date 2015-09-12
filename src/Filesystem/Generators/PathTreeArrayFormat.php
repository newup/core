<?php

namespace NewUp\Filesystem\Generators;

use Illuminate\Support\Str;
use NewUp\Exceptions\InvalidArgumentException;

trait PathTreeArrayFormat
{

    /**
     * Converts the key and path into an array that various tree
     * generators expect.
     *
     * This function does NOT process the path using any renderers.
     *
     * @param $pathKey
     * @param $path
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function getPathForTreeGenerator($pathKey, $path)
    {
        // If the $path is an array, it most likely already contains
        // the information we need, such as if the target is a file or directory.
        if (is_array($path) && array_key_exists('type', $path) && array_key_exists('path', $path)) {
            return $path;
        }

        // At this point, the key should contain at least one ']' character.
        if (!Str::contains($pathKey, ']')) {
            throw new InvalidArgumentException('Missing key options. Supplied key was ' . $pathKey);
        }

        // Since we did not get handed the information we wanted, we have to figure it out
        // by looking at the path key.
        $keyParts          = explode(']', $pathKey);
        $formattingOptions = array_pop($keyParts);
        $type              = 'file';

        if (Str::contains('d', $formattingOptions)) {
            $type = 'dir';
        }

        return [
            'path' => $path,
            'type' => $type
        ];
    }

}