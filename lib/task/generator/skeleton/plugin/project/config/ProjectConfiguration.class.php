<?php

if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->setPlugins(array(
      'sfPropel15Plugin',
      '##PLUGIN_NAME##'
    ));

    $this->setPluginPath('sfPropel15Plugin', dirname(__FILE__).'/../../../../../sfPropel15Plugin');
    $this->setPluginPath('##PLUGIN_NAME##', dirname(__FILE__).'/../../../..');
  }
}