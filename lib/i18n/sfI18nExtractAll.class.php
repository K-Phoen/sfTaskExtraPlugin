<?php

class sfI18nExtractAll extends sfI18nApplicationExtract
{
  /**
   * Configures the current extract object.
   */
  public function configure()
  {
    // by default, no plugin is extracted
    if (!isset($this->parameters['plugins']))
    {
      $this->parameters['plugins'] = array();
    }

    // will create extractor objects for modules
    parent::configure();

    // Plugins
    $this->extractPlugins();

    // forms
    $this->extractForms();
  }

  protected function extractPlugins()
  {
    foreach ($this->parameters['plugins'] as $plugin)
    {
      $this->extractObjects[] = new sfI18nPluginExtractAll(
        $this->i18n,
        $this->culture,
        array('plugin' => $plugin, 'application' => sfConfig::get('sf_app'))
      );
    }
  }

  protected function extractForms()
  {
    $forms = sfFinder::type('file')
      ->relative()
      ->name('*.class.php')
      ->discard(array('sfFormLanguage.class.php', 'sfFormPropelCollection.class.php', 'BaseFormPropel.class.php', 'Base*'))
      ->in(sprintf('%s/form', sfConfig::get('sf_app_lib_dir')));

    foreach ($forms as $form)
    {
      $cleaned_name = str_replace('.class.php', '', basename($form));

      $extractor = new sfI18nFormExtract(
        $this->i18n,
        $this->culture,
        array('form' => $cleaned_name)
      );
      $extractor->extract();

      // will be used later for stats
      $this->extractObjects[] = $extractor;
    }
  }
}
