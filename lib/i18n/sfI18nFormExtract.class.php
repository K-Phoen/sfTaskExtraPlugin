<?php

/**
 * @author    Alexandru Emil Lupu <gang.alecs@gmail.com>
 * @author    Kévin Gomez Pinto <contact@kevingomez.fr>
 *            (modifications & adaptations)
 *
 * Copyright (c) 2009 by Alexandru Emil Lupu
 * Copyright (c) 2011 by Kévin Gomez Pinto <contact@kevingomez.fr>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */



class sfI18nFormExtract extends sfI18nExtract
{
  protected $messages = array();
  protected $form = null;
  protected $ignore_choices_widgets = array('sfWidgetFormPropelChoice');


  public function configure()
  {
    if (!isset($this->parameters['form']))
    {
      throw new sfException('You must give a "form" parameter when extracting for a form.');
    }

    $this->buildFormObject($this->parameters['form']);
  }

  public function extract()
  {
    // possible if the form class is abstract
    if (is_null($this->form))
    {
      return;
    }

    $this->processErrorMessages();
    $this->processLabels();
    $this->processValues();
    $this->processHelp();

    $this->updateMessages($this->messages);
  }

  public function addChoiceWidgetToIgnore($widget)
  {
    arrey_merge($this->ignore_choices_widgets, (array) $widget);
  }

  public function setChoiceWidgetToIgnore($widget)
  {
    $this->ignore_choices_widgets = (array) $widget;
  }

  protected function buildFormObject($class_name)
  {
    if (!class_exists($class_name))
    {
      throw new sfException(sprintf('The %s form does not exist', $class));
    }

    $class = new ReflectionClass($class_name);
    $this->form = $class->isAbstract() ? null : new $class_name();
  }

  /**
   * Browse the form error messages to collect them.
   */
  protected function processErrorMessages()
  {
    $field_list = $this->form->getValidatorSchema()->getFields();
    foreach ($field_list as $field )
    {
      $this->mergeErrorMessages($field);
    }

    $this->mergeErrorMessages($this->form->getValidatorSchema()->getPostValidator());
    $this->mergeErrorMessages($this->form->getValidatorSchema()->getPreValidator());
  }


  protected function mergeErrorMessages($field)
  {
    if (method_exists($field, 'getMessages') && method_exists($field, 'getValidators'))
    {
      $this->messages = array_merge($this->messages, $field->getMessages());
      foreach ($field->getValidators() as $f)
      {
        $this->mergeErrorMessages($f);
      }
    }
    else if (method_exists($field, 'getMessages'))
    {
      $this->messages = array_merge($this->messages, $field->getMessages());
    }

    $this->processMessages();
  }

  protected function processMessages()
  {
    $msg = array();
    foreach ($this->messages as $key => $value)
    {
      $msg[md5($value)] = $value;
    }

    $this->messages = $msg;
  }

  /**
   * Browse the form labels to collect them.
   */
  protected function processLabels()
  {
    $labels = $this->form->getWidgetSchema()->getLabels();
    foreach ($labels as $label_id => $label_value)
    {
      if (empty($value))
      {
        $this->messages[] = $this->form->getWidgetSchema()->getFormFormatter()->generateLabelName($label_id);
      }
      else
      {
        $this->messages[] = $label_value;
      }
    }

    $this->processMessages();
  }

  protected function processValues()
  {
    $widgetSchema = $this->form->getWidgetSchema()->getFields();
    foreach ($widgetSchema as $name => $widget)
    {
      // check if there are choices to process
      if (!($widget instanceof sfWidgetFormChoiceBase))
      {
        continue;
      }

      // and then check if the current widget should be ignored or not
      // some widgets like 'sfWidgetFormPropelChoice' retrieve their values
      // from a DB, so there is no need to translate them here.
      $ignore = false;
      foreach ($this->ignore_choices_widgets as $widget_type)
      {
        if ($widget instanceof $widget_type)
        {
          $ignore = true;
          break;
        }
      }

      // the current widget as been flagged!
      if ($ignore)
      {
        continue;
      }

      // after all this stuff, we can save the choices...
      foreach ($widget->getChoices() as $key => $value)
      {
        $this->messages[] = $value;
      }
    }

    $this->processMessages();
  }

  /**
   * Browse the form help messages to collect them.
   */
  protected function processHelp()
  {
    $helps = $this->form->getWidgetSchema()->getHelps();
    foreach ($helps as $key => $value)
    {
      $this->messages[] = empty($value) ? $key : $value;
    }

    $this->processMessages();
  }
}
