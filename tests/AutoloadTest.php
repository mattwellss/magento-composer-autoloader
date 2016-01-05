<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('BP')) {
            define('BP', __DIR__);
        }

        require_once __DIR__ . '/../src/Autoload.php';
    }

    public function testInstance()
    {
        // This method is used later on, so we should be sure it works
        $this->assertInstanceOf('Varien_Autoload', Varien_Autoload::instance());
    }

    public function testRegisterStandard()
    {
        Varien_Autoload::register();
        $functions = spl_autoload_functions();

        $matches = array_filter($functions, function ($function) {
            return $function == [Varien_Autoload::instance(), 'autoload'];
        });

        $this->assertNotCount(0, $matches);

        // TODO: find a way to use assertArraySubset
        // $this->assertArraySubset(
        //     [Varien_Autoload::instance(), 'autoload'], $functions);

        spl_autoload_unregister([Varien_Autoload::instance(), 'autoload']);
    }

    public function testRegisterWithEnv()
    {
        $_ENV['MAGE_VENDOR_ROOT'] = __DIR__ . '/../vendor';

        Varien_Autoload::register();

        $loader = require __DIR__ . '/../vendor/autoload.php';

        $expectedPaths = [
            BP . '/app/code/local/',
            BP . '/app/code/community/',
            BP . '/app/code/core/',
            BP . '/lib/',
        ];

        // Ensure that by registering the autoloader
        // The expected paths are in the "fallback dirs"
        // For autoloading
        $this->assertArraySubset(
            $expectedPaths, $loader->getFallbackDirs());
    }

    public function testNotFoundAutoload()
    {
        if (!defined('VENDOR_ROOT')) {
            define('VENDOR_ROOT', __DIR__ . '/../vendor');
        }
        $exceptionMessage = 'The composer autoload.php file was not found. See README for more information';

        rename(VENDOR_ROOT . '/autoload.php', VENDOR_ROOT . '/autoload.php.bak');
        try {
            Varien_Autoload::register();
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
            $this->assertEquals($e->getMessage(), $exceptionMessage);
        }
        rename(VENDOR_ROOT . '/autoload.php.bak', VENDOR_ROOT . '/autoload.php');
    }

    public function testRegisterComposer()
    {
        if (!defined('VENDOR_ROOT')) {
            define('VENDOR_ROOT', __DIR__ . '/../vendor');
        }

        Varien_Autoload::register();
        $loader = require __DIR__ . '/../vendor/autoload.php';

        $expectedPaths = [
            BP . '/app/code/local/',
            BP . '/app/code/community/',
            BP . '/app/code/core/',
            BP . '/lib/',
        ];

        // Ensure that by registering the autoloader
        // The expected paths are in the "fallback dirs"
        // For autoloading
        $this->assertArraySubset(
            $expectedPaths, $loader->getFallbackDirs());
    }

}
