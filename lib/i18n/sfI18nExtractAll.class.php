<?php

class sfI18nExtractAll extends sfI18nApplicationExtract
{
  protected $extractObjects = array();


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

    // Application modules
    $moduleNames = sfFinder::type('dir')
      ->maxdepth(0)
      ->relative()
      ->in(sfConfig::get('sf_app_module_dir'));
    foreach ($moduleNames as $moduleName)
    {
      $this->extractObjects[] = new sfI18nModuleExtract(
        $this->i18n,
        $this->culture,
        array('module' => $moduleName)
      );
    }

    // Plugins
    $this->extractPlugins();
  }

  public function extract()
  {
    // Application global templates
    $this->extractFromPhpFiles(sfConfig::get('sf_app_template_dir'));

    // Application global librairies
    $this->extractFromPhpFiles(sfConfig::get('sf_app_lib_dir'));


    // parse all the extractors
    foreach ($this->extractObjects as $extractObject)
    {
      $extractObject->extract();
    }
  }

  protected function extractPlugins()
  {
    foreach ($this->parameters['plugins'] as $plugin)
    {
      $this->extractObjects[] = new sfI18nPluginExtractAll(
        $this->i18n,
        $this->culture,
        array('plugin' => $plugin)
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
