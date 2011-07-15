<?php

if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  protected $dependancies_dir;

  public function setup()
  {
    $this->setPlugins(array(
      'sfPropel15Plugin',

      '##PLUGIN_NAME##'
    ));

    $this->setPluginPath('##PLUGIN_NAME##', dirname(__FILE__).'/../../../..');

    $pluginsDir = sprintf('%s/plugins', sfConfig::get('sf_root_dir'));

    // make sure that the plugins directory exists
    if (!is_dir($pluginsDir))
    {
      $fs = new sfFileSystem();
      $fs->mkdirs($pluginsDir);
    }

    // make sure that all the generated things will go into the plugin's
    // dir
    $this->forceSymlink(dirname(__FILE__).'/../../../..', sprintf('%s/plugins/kNewsPlugin', sfConfig::get('sf_root_dir')));
  }

  public function getPluginPaths()
  {
    if (!isset($this->pluginPaths['']))
    {
      $pluginPaths = $this->getAllPluginPaths();

      $this->pluginPaths[''] = array();
      foreach ($this->getPlugins() as $plugin)
      {
        if (isset($pluginPaths[$plugin]))
        {
          $this->pluginPaths[''][] = $pluginPaths[$plugin];
        }
        // plugin not found, try our dependancies directory
        else if(($path = $this->getDependancy($plugin)))
        {
          $plugin_dir = sprintf('%s/plugins/%s', sfConfig::get('sf_root_dir'), $plugin);

          $this->forceSymlink($path, $plugin_dir);
          $this->pluginPaths[''][] = $plugin_dir;
        }
        else
        {
          throw new InvalidArgumentException(sprintf('The plugin "%s" does not exist.', $plugin));
        }
      }
    }

    return $this->pluginPaths[''];
  }

  protected function forceSymlink($origin, $destination)
  {
    $fs = new sfFileSystem();

    if (file_exists($destination))
    {
      $fs->remove($destination);
    }

    $fs->symlink($origin, $destination);
  }

  protected function guessDependanciesDirectory()
  {
    // if we are not in standelone, this will point to the "parent"
    // symfony project's plugin directory
    $this->dependancies_dir = dirname(__FILE__).'/../../../../../..';

    if (!empty($_SERVER['##PLUGIN_NAME##_DEPENDANCIES']))
    {
      $this->dependancies_dir = $_SERVER['##PLUGIN_NAME##_DEPENDANCIES'];
    }

    // in order to have the propel build system work for external
    // dependancies
    //sfToolkit::addIncludePath($this->dependancies_dir);
    //sfToolkit::addIncludePath(dirname(__FILE__).'/..');
  }

  protected function getDependancy($plugin)
  {
    if (is_null($this->dependancies_dir))
    {
      $this->guessDependanciesDirectory();
    }

    $path = sprintf('%s/plugins/%s', $this->dependancies_dir, $plugin);

    return is_dir($path) ? $path : false;
  }
}