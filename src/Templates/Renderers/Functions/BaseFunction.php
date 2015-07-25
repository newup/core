<?php

namespace NewUp\Templates\Renderers\Functions;

use NewUp\Contracts\Templates\RendererFunction;

abstract class BaseFunction implements RendererFunction
{

    /**
     * The name of the function.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The context of the function.
     *
     * @var null|mixed
     */
    protected $context = null;

    /**
     * Sets the context of the function.
     *
     * @param $context
     *
     * @return mixed
     */
    public function setContext(&$context)
    {
        $this->context = $context;
    }

    /**
     * Gets the name of the function.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}