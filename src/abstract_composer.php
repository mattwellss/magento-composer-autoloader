<?php
/**
 * Use this to allow scripts in `shell` to load composer deps
 */
if (!defined('VENDOR_ROOT')) {
    define('VENDOR_ROOT', dirname(dirname(__DIR__)) . '/vendor');
}

require __DIR__ . '/abstract.php';
