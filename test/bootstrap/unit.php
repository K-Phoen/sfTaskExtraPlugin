<?php

// try to include the command line argument symfony
include_once dirname(__FILE__).'/options.php';

if (!isset($_SERVER['SYMFONY']))
{
  $_SERVER['SYMFONY'] = dirname(__FILE__).'/../../../../lib/vendor/symfony/lib';
}

if (!is_dir($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

// configuration
require_once dirname(__FILE__).'/../fixtures/project/config/ProjectConfiguration.class.php';
$configuration = new ProjectConfiguration(dirname(__FILE__).'/../fixtures/project');
require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

require_once dirname(__FILE__).'/../../config/sfTaskExtraPluginConfiguration.class.php';
$plugin_configuration = new sfTaskExtraPluginConfiguration($configuration, dirname(__FILE__).'/../..');

// autoloader
$autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
$autoload->loadConfiguration(sfFinder::type('file')->name('autoload.yml')->in(array(
  sfConfig::get('sf_symfony_lib_dir').'/config/config',
  sfConfig::get('sf_config_dir'),
)));
$autoload->register();

function task_extra_cleanup()
{
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/cache');
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/log');
}
task_extra_cleanup();
register_shutdown_function('task_extra_cleanup');

// include test task
$autoloader = sfSimpleAutoload::getInstance();
$autoloader->addDirectory(dirname(dirname(dirname(__FILE__))));
$autoloader->register();

