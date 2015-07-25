<?php

namespace NewUp\Tests\Renderer;

use NewUp\Templates\Renderers\TemplateRenderer;

class RenderTestBase extends \PHPUnit_Framework_TestCase
{

    public function getRendererWithTestTemplates()
    {
        $r = $this->getRenderer();
        $r->addPath(__DIR__ . '/Templates/');

        return $r;
    }

    public function getRenderer()
    {
        $renderer = new TemplateRenderer();

        return $renderer;
    }

}