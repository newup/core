<?php

namespace NewUp\Tests\Renderer;

class FilterTest extends RenderTestBase
{

    /**
     * Default filters expected values and the name of the test template file.
     *
     * The files are located in /tests/Renderer/Templates
     *
     * @var array
     */
    protected $defaultFiltersAndExpectedValues = [
        'fooBar'         => 'Test_Filter_Camel',
        'foobar'         => 'Test_Filter_Lower',
        'cars'           => 'Test_Filter_Plural',
        'car'            => 'Test_Filter_Singular',
        'this-is-a-slug' => 'Test_Filter_Slug',
        'foo_bar'        => 'Test_Filter_Snake',
        'FooBar'         => 'Test_Filter_Studly',
        'FOOBAR'         => 'Test_Filter_Upper'
    ];

    public function testDefaultFilters()
    {
        $r = $this->getRendererWithTestTemplates();

        foreach ($this->defaultFiltersAndExpectedValues as $expectedValue => $testTemplate) {
            $this->assertEquals($expectedValue, $r->render($testTemplate));
        }

    }

    /**
     * @expectedException \NewUp\Templates\Renderers\InvalidSyntaxException
     */
    public function testRendererThrowsSyntaxErrorExceptionWhenFilterDoesNotExist()
    {
        $r = $this->getRenderer();
        $r->renderString('{{ ""|bad_filter_name }}');
    }

    public function testPluralFilterParameters()
    {
        $r = $this->getRenderer();
        $this->assertEquals('car', $r->renderString('{{ "car"|plural(1) }}'));
        $this->assertEquals('cars', $r->renderString('{{ "car"|plural(2) }}'));
        $this->assertEquals('cars', $r->renderString('{{ "car"|plural }}'));
    }

    public function testSlugFilterParameters()
    {
        $r = $this->getRenderer();
        $this->assertEquals('this-is-a-slug', $r->renderString('{{ "this is a slug"|slug("-") }}'));
        $this->assertEquals('this_is_a_slug', $r->renderString('{{ "this is a slug"|slug("_") }}'));
    }

    public function testSnakeFilterParameters()
    {
        $r = $this->getRenderer();
        $this->assertEquals('snake_test', $r->renderString('{{ "snakeTest"|snake("_") }}'));
        $this->assertEquals('snake-test', $r->renderString('{{ "snakeTest"|snake("-") }}'));
    }

}