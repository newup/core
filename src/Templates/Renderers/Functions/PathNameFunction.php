<?php

namespace NewUp\Templates\Renderers\Functions;

use NewUp\Templates\Renderers\TemplateRenderer;

class PathNameFunction extends BaseFunction
{

    /**
     * The name of the function.
     *
     * @var string
     */
    protected $name = 'path';

    /**
     * The function context (TemplateRenderer).
     *
     * @var TemplateRenderer
     */
    protected $context = null;

    /**
     * Gets the function that is passed to the rendering environment.
     *
     * @return mixed|\Closure
     */
    public function getFunction()
    {
        return new \Twig_SimpleFunction('path', function ($pathName) {
            $data = $this->context->getData();

            if (array_key_exists('sys_pathNames', $data)) {
                if (array_key_exists($pathName, $data['sys_pathNames'])) {
                    return $this->context->renderString($data['sys_pathNames'][$pathName]);
                }

                return '';
            }

            return '';
        });
    }


}