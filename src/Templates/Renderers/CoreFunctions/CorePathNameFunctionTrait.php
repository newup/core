<?php

namespace NewUp\Templates\Renderers\CoreFunctions;

trait CorePathNameFunctionTrait
{

    private function getCorePathNameFunction()
    {
        return new \Twig_SimpleFunction('path', function ($pathName) {

            $data = $this->getData();

            if (array_key_exists('sys_pathNames', $data)) {
                if (array_key_exists($pathName, $data['sys_pathNames'])) {
                    return $this->renderString($data['sys_pathNames'][$pathName]);
                }

                return '';
            }

            return '';
        });
    }

}