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
}
