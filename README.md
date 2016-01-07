# Magento Composer Autoloader

Complete and simple to install Composer autoloader or Magento

## Why Does This Even Exist?

1. Other Composer autoloader integrations aren't complete. The most common issues with other implementations:
  - They allow both Magento and Composer to register autoloaders. This causes problems when using libraries that expect `class_exists` to work properly&mdash;Magento's will **always** attempt to include the file.
  - They allow Magento's autoload to register itself, then manually de-register it. This is wholly unnecessary overhead.
  - They require manual configuration of `composer.json` for Magento autoloading.
2. With more and more need to integrate namespaced systems with Magento, a `require`-able, redistributable, easy to set up module is needed

## Installation

Until it's listed in packagist or firegento, add the repository to your `composer.json`:
```json
{
    "type": "vcs",
    "url": "https://github.com/mattwellss/magento-composer-autoloader"
}
```

Add the library to your `require` section:
```json
"require": {
    "mattwellss/magento-composer-autoloader": "~1.0"
}
```

## Configuration

Post install, the new `Varien_Autoload` class needs to know where `vendor/autoload.php` is hiding. This must be configured.

### Defining the vendor root directory

Regardless of how the vendor dir is defined, it **must not end with a trailing slash**.

The two methods of describing the vendor root directory are:
- `VENDOR_ROOT` constant. Must be defined _in all entry points_.
- `MAGE_VENDOR_ROOT` env variable. Be sure it's defined for **all** PHP environments (cli and fpm/mod_php).

## Optimizing the Autoloader

Normally, `composer dump -o` is all one does to optimize a composer autoloader. However, running `php shell/classmap_generator.php` when deploying new code to production is recommended. The script generates a classmap file to be used by composer to speed autoloading of files in Magento's code pools.

To enable use of the generated classmap, include `define('OPTIMIZED_COMPOSER', true)` in `includes/config.php`.
