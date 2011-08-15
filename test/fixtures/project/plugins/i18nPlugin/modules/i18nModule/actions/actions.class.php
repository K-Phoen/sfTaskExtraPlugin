<?php

require_once(dirname(__FILE__).'/../lib/Basei18nActions.class.php');

class i18nActions extends Basei18nActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->foo = __('Set in "index" action...but in the actions.class.php file');
  }
}