<?php

/**
 * ##PLUGIN_NAME## configuration.
 *
 * @package     ##PLUGIN_NAME##
 * @subpackage  config
 * @author      ##AUTHOR_NAME##
 * @version     SVN: $Id$
 */
class ##PLUGIN_NAME##Configuration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect(
      'user.method_not_found',
      array('##PLUGIN_NAME##User', 'methodNotFound'));

    if (sfConfig::get('app_##PLUGIN_NAME##_routes_register', true))
    {
      $this->dispatcher->connect(
        'routing.load_configuration',
        array('##PLUGIN_NAME##Routing', 'listenToRoutingLoadConfigurationEvent'));
    }
  }

  /**
   * Listens for the "task.test.filter_test_files" event and adds tests from the current plugin.
   *
   * @param  sfEvent $event
   * @param  array   $files
   *
   * @return array An array of files with the appropriate tests from the current plugin merged in
   */
  public function filterTestFiles(sfEvent $event, $files)
  {
    $task = $event->getSubject();

    if ($task instanceof sfTestAllTask)
    {
      $directory = $this->rootDir.'/test';
      $names = array();
    }
    else if ($task instanceof sfTestFunctionalTask)
    {
      $directory = $this->rootDir.'/test/functional';
      $names = $event['arguments']['controller'];
    }
    else if ($task instanceof sfTestUnitTask)
    {
      $directory = $this->rootDir.'/test/unit';
      $names = $event['arguments']['name'];
    }

    if (!count($names))
    {
      $names = array('*');
    }

    foreach ($names as $name)
    {
      // we have to limit the max depth because of the symbolic links
      // in test/fixtures/project/plugins/*
      $finder = sfFinder::type('file')->follow_link()->maxdepth(1)->name(basename($name).'Test.php');
      $files = array_merge($files, $finder->in($directory.'/'.dirname($name)));
    }

    return array_unique($files);
  }
}