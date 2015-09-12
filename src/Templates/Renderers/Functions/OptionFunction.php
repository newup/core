<?php

namespace NewUp\Templates\Renderers\Functions;

use NewUp\Templates\Renderers\TemplateRenderer;

class OptionFunction extends BaseFunction
{

    /**
     * The name of the function.
     *
     * @var string
     */
    protected $name = 'option';

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
        return function ($argument, $default = null) {
            return array_get($this->context->getData(), 'user_options.' . $argument, $default);
        };
    }

}