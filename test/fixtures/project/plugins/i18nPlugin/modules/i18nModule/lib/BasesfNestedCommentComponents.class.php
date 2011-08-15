<?php
class Basei18nComponents extends sfComponents
{
  public function executeFoo()
  {
    $this->bar = __('Set in "fooComponent" component');
  }
}
