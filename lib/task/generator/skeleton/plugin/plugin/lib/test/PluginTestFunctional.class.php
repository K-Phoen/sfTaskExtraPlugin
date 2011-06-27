<?php

/**
 * This file declare the ##PLUGIN_NAME##TestFunctional class.
 *
 * @package     ##PLUGIN_NAME##
 * @subpackage  test
 * @author      ##AUTHOR_NAME##
 * @version     SVN: $Id$
 */

/**
 * Functional tester to use in ##PLUGIN_NAME## tests
 */
class ##PLUGIN_NAME##TestFunctional extends sfTestFunctional
{
  public function loadData($data=null)
  {
    $this->info('Loading data ...');

    $data = is_null($data) ? sfConfig::get('sf_data_dir').'/fixtures' : $data;

    $task = new sfPropelBuildAllLoadTask($this->browser->getContext()->getEventDispatcher(), new sfAnsiColorFormatter());
    $task->run(array(), array(
      'no-confirmation' => true
    ));

    $loader = new sfPropelData();
    $loader->loadData($data);

    return $this;
  }
}
