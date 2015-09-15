<?php namespace NewUp\Tests\Support;

use Illuminate\Contracts\Config\Repository as ConfigRepository;

class HelpersTest extends \PHPUnit_Framework_TestCase
{

    public function testArrayRemoveValueRemovesCorrectValue()
    {
        $array = [
            'test',
            'test2'
        ];

        array_remove_value($array, 'test2');

        $this->assertEquals('test', $array[0]);
    }

    public function testGetUserConfigPathReturnsDefault()
    {
        $value = get_user_config_path();
        $this->assertEquals(config_path('user'), $value);
    }

    public function testGetUserConfigObservesConfigConstant()
    {
        define('NEWUP_CORE_USER_CONFIGURATION_DIRECTORY', 'test');
        $this->assertEquals('test', get_user_config_path());
    }

    public function testUserConfigReturnsConfigRepository()
    {
        $instance = user_config();
        $this->assertInstanceOf(ConfigRepository::class, $instance);
    }

    public function testUserConfigSettingAndGettingWorks()
    {
        user_config([
           'test' => 'test_value'
        ]);

        $this->assertEquals('test_value', user_config('test'));
    }

    public function testLoadSystemTemplateReturnsCorrectContent()
    {
        $content = load_system_template('Example');
        $this->assertEquals('{# This is just an example. Do not use. #}', $content);
    }

}