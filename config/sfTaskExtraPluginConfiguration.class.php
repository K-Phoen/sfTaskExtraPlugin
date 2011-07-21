<?php

/**
 * Plugin configuration.
 *
 * @package     sfTaskExtraPlugin
 * @subpackage  config
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfTaskExtraPluginConfiguration extends sfPluginConfiguration
{
  const
    VERSION = '1.3.4-DEV';

  protected
    $connectedPlugins = array();

  /**
   * @see sfPluginConfiguration
   */
  public function configure()
  {
    $this->dispatcher->connect('command.pre_command', array($this, 'listenForPreCommand'));
    $this->dispatcher->connect('command.post_command', array($this, 'listenForPostCommand'));
  }

  /**
   * Listens for the 'command.pre_command' event.
   *
   * @param   sfEvent $event
   *
   * @return  boolean
   */
  public function listenForPreCommand(sfEvent $event)
  {
    $task = $event->getSubject();
    $arguments = $event['arguments'];
    $options = $event['options'];

    // set global symfony path for plugin tests
    if ('test' == $task->getNamespace())
    {
      $_SERVER['SYMFONY'] = sfConfig::get('sf_symfony_lib_dir');
    }

    return false;
  }

  /**
   * Listens for the 'command.post_command' event.
   *
   * @param   sfEvent $event
   *
   * @return  boolean
   */
  public function listenForPostCommand(sfEvent $event)
  {
    $task = $event->getSubject();

    if ($task instanceof sfPropelBuildModelTask)
    {
      $addon = new sfTaskExtraBuildModelAddon($this->configuration, new sfAnsiColorFormatter());
      $addon->setWrappedTask($task);
      $addon->executeAddon();

      return true;
    }

    return false;
  }
}
