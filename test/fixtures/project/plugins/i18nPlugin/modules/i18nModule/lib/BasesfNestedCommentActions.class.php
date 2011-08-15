<?php
class Basei18nActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->foo = __('Set in "index" action');
  }
}