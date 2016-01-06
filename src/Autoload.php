<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_Autoload
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Classes source autoload
 */
class Varien_Autoload
{
    const SCOPE_FILE_PREFIX = '__';

    static protected $_instance;
    static protected $_scope = 'default';
    static protected $_classMap = null;

    protected $_isIncludePathDefined= null;
    protected $_collectClasses      = false;
    protected $_collectPath         = null;
    protected $_arrLoadedClasses    = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        register_shutdown_function(array($this, 'destroy'));
        $this->_isIncludePathDefined = defined('COMPILER_INCLUDE_PATH');
        if (defined('COMPILER_COLLECT_PATH')) {
            $this->_collectClasses  = true;
            $this->_collectPath     = COMPILER_COLLECT_PATH;
        }
        self::registerScope(self::$_scope);
    }

    /**
     * Singleton pattern implementation
     *
     * @return Varien_Autoload
     */
    static public function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new Varien_Autoload();
        }
        return self::$_instance;
    }

    /**
     * Returns vendor root directory
     * @return string|false
     */
    public static function getVendorRootDir()
    {
        if (getenv('MAGE_VENDOR_ROOT')) {
            return getenv('MAGE_VENDOR_ROOT');
        }

        if (defined('VENDOR_ROOT')) {
            return VENDOR_ROOT;
        }

        return false;
    }

    /**
     * Get an instance of the Composer Autoloader
     *
     * @return Composer\Autoload\ClassLoader
     * @throws Exception
     */
    private static function getComposerAutoloader()
    {
        $vendorDir = self::getVendorRootDir();

        $autoloadFilename = $vendorDir . '/autoload.php';
        if (!file_exists($autoloadFilename)) {
            throw new Exception(
                'The composer autoload.php file was not found. See README for more information');
        }

        return require $autoloadFilename;
    }

    /**
     * Register SPL autoload function
     */
    static public function register()
    {
        if (!self::getVendorRootDir()) {
            spl_autoload_register(array(self::instance(), 'autoload'));
            return;
        }

        self::registerComposerAutoloader();
    }

    /**
     * Add Magento's paths to the Composer autoload directory fallback
     * @return void
     * @throws Exception
     */
    private static function registerComposerAutoloader()
    {
        $autoloader = self::getComposerAutoloader();

        if (defined('OPTIMIZED_COMPOSER')) {
            self::registerClassMap($autoloader);
            return;
        }

        $paths = [
            BP . '/app/code/local/',
            BP . '/app/code/community/',
            BP . '/app/code/core/',
            BP . '/lib/',
        ];

        $autoloader->add('', $paths, true);
    }

    /**
     * Prepare for classmap autoloading, you speedster!
     * @param  Composer\Autoload\ClassLoader $autoloader
     * @return void
     */
    public static function registerClassMap($autoloader)
    {
        self::$_classMap = include BP.'/includes/optimized_map.php';

        if (self::$_classMap) {
            $autoloader->addClassMap(self::$_classMap);
        }
    }

    /**
     * Load class source code
     *
     * @param string $class
     */
    public function autoload($class)
    {
        if ($this->_collectClasses) {
            $this->_arrLoadedClasses[self::$_scope][] = $class;
        }
        if ($this->_isIncludePathDefined) {
            $classFile =  COMPILER_INCLUDE_PATH . DIRECTORY_SEPARATOR . $class;
        } else {
            $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)));
        }
        $classFile.= '.php';
        //echo $classFile;die();
        return include $classFile;
    }

    /**
     * Register autoload scope
     * This process allow include scope file which can contain classes
     * definition which are used for this scope
     *
     * @param string $code scope code
     */
    static public function registerScope($code)
    {
        self::$_scope = $code;
        if (defined('COMPILER_INCLUDE_PATH')) {
            @include COMPILER_INCLUDE_PATH . DIRECTORY_SEPARATOR . self::SCOPE_FILE_PREFIX.$code.'.php';
        }
    }

    /**
     * Get current autoload scope
     *
     * @return string
     */
    static public function getScope()
    {
        return self::$_scope;
    }

    /**
     * Class destructor
     */
    public function destroy()
    {
        if ($this->_collectClasses) {
            $this->_saveCollectedStat();
        }
    }

    /**
     * Save information about used classes per scope with class popularity
     * Class_Name:popularity
     *
     * @return Varien_Autoload
     */
    protected function _saveCollectedStat()
    {
        if (!is_dir($this->_collectPath)) {
            @mkdir($this->_collectPath);
            @chmod($this->_collectPath, 0777);
        }

        if (!is_writeable($this->_collectPath)) {
            return $this;
        }

        foreach ($this->_arrLoadedClasses as $scope => $classes) {
            $file = $this->_collectPath.DIRECTORY_SEPARATOR.$scope.'.csv';
            $data = array();
            if (file_exists($file)) {
                $data = explode("\n", file_get_contents($file));
                foreach ($data as $index => $class) {
                    $class = explode(':', $class);
                    $searchIndex = array_search($class[0], $classes);
                    if ($searchIndex !== false) {
                        $class[1]+=1;
                        unset($classes[$searchIndex]);
                    }
                    $data[$index] = $class[0].':'.$class[1];
                }
            }
            foreach ($classes as $class) {
                $data[] = $class . ':1';
            }
            file_put_contents($file, implode("\n", $data));
        }
        return $this;
    }
}
