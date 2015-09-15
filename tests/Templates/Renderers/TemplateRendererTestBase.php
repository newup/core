<?php

namespace NewUp\Tests\Templates\Renderers;

use NewUp\Templates\Renderers\TemplateRenderer;

class TemplateRendererTestBase extends \PHPUnit_Framework_TestCase
{

    public function getRendererWithTestTemplates()
    {
        $r = $this->getRenderer();
        $r->addPath(getFixturePath('Templates'));

        return $r;
    }

    public function getRenderer()
    {
        $renderer = new TemplateRenderer();

        return $renderer;
    }

}