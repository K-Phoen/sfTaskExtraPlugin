<?php

class OtherI18nForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'CoolField'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'CoolField'   => new sfValidatorString(
        array('required' => true),
        array('required' => 'Please, fill this field ...')
      ),
    ));

    $this->widgetSchema['CoolField']->setLabel('Cool Field');

    $this->widgetSchema->setNameFormat('bar[%s]');
  }
}
