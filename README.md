# Magento Composer Autoloader

Complete and simple to install Composer autoloader or Magento

## Why Does This Even Exist?

1. Other Composer autoloader integrations aren't complete. The most common issues with other implementations:
  - They allow both Magento and Composer to register autoloaders. This causes problems when using libraries that expect `class_exists` to work properly&mdash;Magento's will **always** attempt to include the file.
  - They allow Magento's autoload to register itself, then manually de-register it. This is wholly unnecessary overhead.
  - They require manual configuration of `composer.json` for Magento autoloading.
2. With more and more need to integrate namespaced systems with Magento, a `require`-able, redistributable, easy to set up module is needed

## Installation

### Get The Files

Add `"mattwells/magento-composer-autoloader": "~0.1"` to your `composer.json`'s `require` node. Also, **don't forget** to add the following to `repositories`!
```json
{
    "type": "vcs",
    "url": "https://github.com/mattwellss/magento-composer-autoloader"
}
```

This should only be a temporary solution until the project is listed on a real composer registry!

## Configuration

Post install, the new `Varien_Autoload` class needs one more setting to use Composer instead of Magento to autoload: The `VENDOR_ROOT` constant. Generally it's best to configure this in your index file as well as any other entrypoints, such as `cron.php`, `api.php` and others.

### `VENDOR_ROOT`

The `VENDOR_ROOT` constant is the absolute path to your project's `vendor` directory, such that `VENDOR_ROOT . '/autoload.php'` resolves correctly to Composer autoload file. It'd be real nice to not put this config on you our users, but there's no known way around it.

## Optimizing the Autoloader

Aside from `composer dump -o`, running `php shell/classmap_generator.php` when deploying new code to production is recommended. That script generates a classmap file to be used by composer to speed autoloading of files in Magento's code pools.

To enable use of the generated classmap, include `define('OPTIMIZED_COMPOSER', true)` in `includes/config.php`.
