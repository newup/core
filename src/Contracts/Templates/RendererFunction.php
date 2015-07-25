<?php

namespace NewUp\Contracts\Templates;

interface RendererFunction
{

    /**
     * Sets the context of the function.
     *
     * @param $context
     * @return mixed
     */
    public function setContext(&$context);

    /**
     * Gets the name of the function.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the function that is passed to the rendering environment.
     *
     * @return mixed|\Closure
     */
    public function getFunction();

}