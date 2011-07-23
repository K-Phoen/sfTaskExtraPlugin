<?php

class FooI18nForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'title'       => new sfWidgetFormInputText(),
      'content'     => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'title'       => new sfValidatorString(array('max_length' => 255)),
      'content'     => new sfValidatorString(
        array('required' => true),
        array('required' => 'Try to fill this field ...')
      ),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPass(array('required'=> true), array('required' => 'Oh! this validator is a very dumb one'))
    );

    $this->widgetSchema->setNameFormat('foo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}
