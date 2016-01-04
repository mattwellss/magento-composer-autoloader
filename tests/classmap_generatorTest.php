<?php

class classmap_generatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!defined('BP')) {
            define('BP', __DIR__);
        }

        // Set recursive = true for the first app/code pool
        mkdir(BP . '/app/code/local', 0777, true);
        mkdir(BP . '/app/code/community');
        mkdir(BP . '/app/code/core');
        mkdir(BP . '/lib');
        mkdir(BP . '/includes');
    }

    public function testRun()
    {
        require __DIR__ . '/../src/classmap_generator.php';
        $this->assertFileExists(BP . '/includes/optimized_map.php');

        $map = require BP . '/includes/optimized_map.php';

        // The map is empty since we created an empty Magento structure
        $this->assertEquals([], $map);
    }

    public function tearDown()
    {
        rmdir(BP . '/app/code/local');
        rmdir(BP . '/app/code/community');
        rmdir(BP . '/app/code/core');
        rmdir(BP . '/app/code');
        rmdir(BP . '/app');
        rmdir(BP . '/lib');
        unlink(BP . '/includes/optimized_map.php');
        rmdir(BP . '/includes');
    }
}
