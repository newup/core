<?php

use NewUp\Tests\Templates\Renderers\TemplateRendererTestBase;
use NewUp\Contracts\DataCollector;
use NewUp\Templates\Renderers\Collectors\FileNameCollector;
use NewUp\Support\Testing\RequiredPhpVersionSkipTrait;

class RenderDataCollectionTest extends TemplateRendererTestBase
{

    use RequiredPhpVersionSkipTrait;

    public function testGetCollectorsReturnsArray()
    {
        $this->assertInternalType('array', $this->getRenderer()->getCollectors());
    }

    public function testRendererAcceptsCollectors()
    {
        $r = $this->getRenderer();
        $collector = new FileNameCollector;
        $r->addCollector($collector);
        $this->assertCount(1, $r->getCollectors());
    }

    public function testRendererDataCollectionReturnsArray()
    {
        $r = $this->getRenderer();
        $this->assertInternalType('array', $r->collectData());
    }

    public function testRenderDataCollectionReturnsDataFromCollectors()
    {
        $r = $this->getRenderer();
        $firstCollector = new FileNameCollector;
        $firstCollector->addFileNames(['sample' => 'file']);

        $r->addCollector($firstCollector);
        $r->addCollector(new DummyCollector);

        $collectedData = $r->collectData();
        $this->assertArrayHasKey('dummy', $collectedData);
        $this->assertArrayHasKey('sys_pathNames', $collectedData);
    }

    public function testRenderMergesCollectorDataWithEnvironmentData()
    {
        $r = $this->getRenderer();
        $r->addCollector(new DummyCollector());
        $data = $r->getData();
        $this->assertArrayHasKey('dummy', $data);
    }

    /**
     * @expectedException Exception
     */
    public function testRenderOnlyAcceptsDataCollectors()
    {
        $this->requirePhpVersion('<', '7');
        $r = $this->getRenderer();
        $r->addCollector((new \stdClass()));
    }

    /**
     * @expectedException TypeError
     */
    public function testRenderOnlyAcceptsDataCollectorsAndThrowsTypeErrors()
    {
        $this->requirePhpVersion('>=', '7');
        $this->testRenderOnlyAcceptsDataCollectors();
    }

}

class DummyCollector implements DataCollector
{

    /**
     * Returns an array of data that should be merged with the rendering environment.
     *
     * @return array
     */
    public function collect()
    {
        return ['dummy' => 'data'];
    }


}