<?php

use NewUp\Tests\Templates\Renderers\TemplateRendererTestBase;
use NewUp\Foundation\Application;

class TemplateRendererTest extends TemplateRendererTestBase
{

    public function testSystemCorePathIsAvailableByDefault()
    {
        $r = $this->getRenderer();

        $paths = $r->getPaths();
        $this->assertCount(1, $paths);
        $this->assertStringEndsWith('system/core', $paths[0]);
    }

    public function testAddPathsWork()
    {
        $r = $this->getRenderer();

        $r->addPath(storage_path() . '/templates');
        $this->assertCount(2, $r->getPaths());

        $r->addPath(storage_path() . '/templates/store');
        $this->assertCount(3, $r->getPaths());
    }

    /**
     * @expectedException \NewUp\Exceptions\InvalidPathException
     */
    public function testAddPathsThrowsExceptionWhenAnInvalidPathIsAdded()
    {
        $r = $this->getRenderer();
        $r->addPath('this path does not exist!');
    }

    public function testDefaultEnvironmentVariablesArePresent()
    {
        $r = $this->getRenderer();
        $this->assertArrayHasKey('newup_version', $r->getData());
    }

    public function testSettingEnvironmentDataWorks()
    {
        $r = $this->getRenderer();
        $r->setData('custom', 'value');
        $this->assertArrayHasKey('custom', $r->getData());

        $this->assertEquals('value', $r->getData()['custom']);
    }

    public function testEnvironmentAndSystemDataVariablesAreMerged()
    {
        $r = $this->getRenderer();
        $r->setData('custom', 'value');

        $this->assertArrayHasKey('newup_version', $r->getData());
        $this->assertArrayHasKey('custom', $r->getData());

        $this->assertEquals('value', $r->getData()['custom']);
        $this->assertEquals(Application::VERSION, $r->getData()['newup_version']);
    }

    public function testRenderFileTemplatesWorks()
    {
        $r = $this->getRendererWithTestTemplates();
        $this->assertEquals('Not affected.', $r->render('Test_Simple'));
    }

    public function testDataIsPassedToTemplates()
    {
        $r = $this->getRendererWithTestTemplates();
        $r->setData('custom', 'Hello world');
        $r->setData('another', '!');
        $this->assertEquals('Hello world', $r->render('Test_Data'));
        $this->assertEquals('Hello world!', $r->render('Test_Data_Multiple'));
        $this->assertEquals('Hello world' . Application::VERSION, $r->render('Test_Data_Merged'));
    }

    /**
     * @expectedException \NewUp\Templates\Renderers\InvalidTemplateException
     */
    public function testIncorrectTemplateThrowsException()
    {
        $r = $this->getRendererWithTestTemplates();
        $r->render('This does not exist!');
    }

    /**
     * @expectedException \NewUp\Templates\Renderers\InvalidSyntaxException
     */
    public function testIncorrectSyntaxThrowsException()
    {
        $r = $this->getRendererWithTestTemplates();
        $r->render('Test_Syntax_Error');
    }

    public function testRenderStringAllowsRegularStrings()
    {
        $r = $this->getRenderer();
        $this->assertEquals('Not affected.', $r->renderString('Not affected.'));
    }

    public function testRenderStringWorksWithData()
    {
        $r = $this->getRenderer();
        $r->setData('custom', 'value');
        $this->assertEquals('value', $r->renderString('{{ custom }}'));
    }

    /**
     * @expectedException \NewUp\Templates\Renderers\InvalidSyntaxException
     */
    public function testRenderStringThrowSyntaxException()
    {
        $r = $this->getRenderer();
        $r->renderString('{{');
    }

    public function testRendererDoesNotEscapeByDefault()
    {
        $r = $this->getRenderer();

        $testString = '<script></script><html></html>';

        $r->setData('test', $testString);
        $this->assertEquals($testString, $r->renderString('{{ test }}'));
    }

    public function testTemplatesCanUseInheritence()
    {
        $r = $this->getRendererWithTestTemplates();
        $value = $r->render('Test_Child');
        $this->assertEquals('This is a base template.', $value);
    }

    public function testTemplatesCanUseInheritenceBlocks()
    {
        $r = $this->getRendererWithTestTemplates();
        $value = $r->render('Test_Block_Child');
        $this->assertStringEqualsFile(getFixturePath('Templates/Test_Block_Child_Expected'), $value);
    }

    public function testTemplatesCanIncludeSystemTemplates()
    {
        $r = $this->getRendererWithTestTemplates();
        $this->assertEquals(load_core_template('newup_notice'), $r->render('Test_Core_Include'));
    }

}