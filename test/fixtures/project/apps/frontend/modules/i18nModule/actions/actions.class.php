<?php


class i18nActions extends Basei18nActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->foo = __('Set in "index" action... in the i18nModule of the frontend app');
  }
}