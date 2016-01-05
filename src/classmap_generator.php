<?php
/**
 * @author Matthew Wells
 */

use Composer\Autoload\ClassMapGenerator;

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mattwellss_MagentoComposerAutoloader_Compiler extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        $mapDirs = array(
            BP . '/app/code/local',
            BP . '/app/code/community',
            BP . '/app/code/core',
            BP . '/lib');

        ClassMapGenerator::dump($mapDirs, BP . '/includes/optimized_map.php');
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f classmap_generator.php

USAGE;
    }
}

$shell = new Mattwellss_MagentoComposerAutoloader_Compiler();
$shell->run();
