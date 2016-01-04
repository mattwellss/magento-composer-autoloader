<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('VENDOR_ROOT')) {
            define('VENDOR_ROOT', __DIR__ . '/../vendor');
        }

        if (!defined('BP')) {
            define('BP', __DIR__);
        }

        require_once __DIR__ . '/../src/Autoload.php';
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Varien_Autoload', Varien_Autoload::instance());
    }

    public function testNotFoundAutoload()
    {
        $exceptionMessage = VENDOR_ROOT . '/autoload.php was not found. Is "VENDOR_ROOT" correctly defined?';

        rename(VENDOR_ROOT . '/autoload.php', VENDOR_ROOT . '/autoload.php.bak');
        try {
            Varien_Autoload::registerComposerAutoloader();
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
            $this->assertEquals($e->getMessage(), $exceptionMessage);
        }
        rename(VENDOR_ROOT . '/autoload.php.bak', VENDOR_ROOT . '/autoload.php');
    }

    public function testRegisterComposer()
    {
        Varien_Autoload::registerComposerAutoloader();
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
