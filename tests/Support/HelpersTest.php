<?php namespace NewUp\Tests\Support;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Composer\Autoload\ClassLoader;

class HelpersTest extends \PHPUnit_Framework_TestCase
{

    public $shouldNotBeAccessible = true;

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

    public function testPackageVendorNamespaceCombinesPartsCorrectly()
    {
        $this->assertEquals('Test\\Test', package_vendor_namespace('Test', 'Test'));
        $this->assertEquals('Acme\\Package', package_vendor_namespace('Acme', 'Package'));
    }

    public function testPackageVendorNamespaceCombinesPartsCorrectlyForAutoloaders()
    {
        $this->assertEquals('Test\\Test\\', package_vendor_namespace('Test', 'Test', true));
        $this->assertEquals('Acme\\Package\\', package_vendor_namespace('Acme', 'Package', true));
    }

    public function testGetComposerLoaderReturnsClassLoaderInstance()
    {
        $this->assertInstanceOf(ClassLoader::class, get_composer_loader());
    }

    public function testAddingPsr4AddsEntry()
    {
        $loader = get_composer_loader();
        $currentPsr4Count = count($loader->getPrefixesPsr4());
        add_psr4('Test\\Test\\', '.');
        $newPsr4Count = count($loader->getPrefixesPsr4());
        $this->assertNotEquals($currentPsr4Count, $newPsr4Count);
        $this->assertArrayHasKey('Test\\Test\\', $loader->getPrefixesPsr4());
    }

    public function testScopeIncludeIncludesFiles()
    {
        $this->assertFalse(defined('FROM_SCOPED_INCLUDE'));
        scope_include(getFixturePath('Support/scoped_include.php'));
        $this->assertTrue(defined('FROM_SCOPED_INCLUDE'));
    }

    public function testScopeIncludeDoesNotHaveAccessToCurrentClass()
    {
        $results = scope_include(getFixturePath('Support/unsafe_scoped_include.php'));
        // Results will be true when included normally.
        $this->assertNull($results);
    }

    public function testRemoveAnsiRemovesAnsiCharactersFromString()
    {
        $results = remove_ansi(chr(27) . "[1mBold " . chr(27) . "[31;42mon green" . chr(27) . "[0m" . chr(27) . "[m");
        $this->assertEquals('Bold on green', $results);
    }

    public function testNormalizeLineEndingsRemovesCarriageReturns()
    {
        $this->assertEquals("\n", normalize_line_endings("\r\n"));
        $this->assertEquals("\n", normalize_line_endings("\r"));
    }

}