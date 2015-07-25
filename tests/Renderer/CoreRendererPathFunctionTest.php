<?php

namespace NewUp\Tests\Renderer;

use NewUp\Templates\Renderers\Collectors\FileNameCollector;

class CoreRendererPathFunctionTest extends RenderTestBase
{

    public function testPathFunctionReturnsEmptyStringIfPathDoesNotExist()
    {
        $r = $this->getRenderer();
        $c = $this->getCollector();

        $this->assertEquals('', $r->renderString('{{ path("x") }}'));
        $c->addFileNames(['test' => '{{ "test_me"|studly }}']);

    }

    private function getCollector()
    {
        $c = new FileNameCollector;
        $c->addFileNames(['test' => '{{ "test_me"|studly }}']);

        return $c;
    }

    public function testPathFunctionReturnsParsedPathName()
    {
        $r = $this->getRenderer();
        $c = $this->getCollector();
        $r->addCollector($c);

        $this->assertEquals('TestMe', $r->renderString('{{ path("test") }}'));
    }

}