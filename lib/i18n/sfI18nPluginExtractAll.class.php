<?php

class sfI18nPluginExtractAll extends sfI18nApplicationExtract
{
  protected $plugin;
  protected $pluginPath;

  /**
   * Configures the current extract object.
   */
  public function configure()
  {
    if (!isset($this->parameters['plugin']))
    {
      throw new sfException('You must give a "plugin" parameter when extracting for a plugin.');
    }

    $this->plugin = $this->parameters['plugin'];
    $this->pluginPath = sprintf('%s/%s', sfConfig::get('sf_plugins_dir'), $this->plugin);

    $this->i18n->setMessageSource($this->getMessageSource(), $this->culture);
  }

  protected function getMessageSource()
  {
    $options = $this->i18n->getOptions();
    $source = isset($this->parameters['application'])
      ? sfConfig::get('sf_app_i18n_dir')
      : sprintf('%s/i18n', $this->pluginPath);

    return $this->i18n->isMessageSourceFileBased($options['source']) ? array($source) : null;
  }

  /**
   * Extracts i18n strings in all the possible locations of the plugin.
   * It looks:
   * * in modules
   * * in conf/generator.yml
   * * in validate/*.yml
   *
   * @see sf18nPluginModuleExtract
   */
  public function extract()
  {
    // Extract from PHP files to find __() calls in actions/ lib/ and templates/ directories
    $this->extractModules();

    // Extract form forms (error messages, labels, help texts, etc...)
    $this->extractForms();

    // Extract from generator.yml files
    $this->extractGeneratorYml();

    // Extract from validate/*.yml files
    $this->extractValidateYml();
  }

  protected function extractForms()
  {
    $forms = sfFinder::type('file')
      ->relative()
      ->name('*.class.php')
      ->discard(array('BaseFormPropel.class.php', 'Base*'))
      ->in(sprintf('%s/lib/form', $this->pluginPath));

    foreach ($forms as $form)
    {
      $cleaned_name = str_replace('.class.php', '', basename($form));

      $extractor = new sfI18nFormExtract(
        clone $this->i18n,
        $this->culture,
        array('form' => $cleaned_name)
      );
      $extractor->extract();

      // will be used later for stats
      $this->extractObjects[] = $extractor;
    }
  }

  protected function extractModules()
  {
    $modules = sfFinder::type('dir')
      ->relative()
      ->maxdepth(0)
      ->in(sprintf('%s/modules', $this->pluginPath));

    foreach ($modules as $module)
    {
      $extractor = new sfI18nPluginModuleExtract(
        clone $this->i18n,
        $this->culture,
        array(
          'plugin' => $this->plugin,
          'module' => $module
        )
      );
      $extractor->extract();

      // will be used later for stats
      $this->extractObjects[] = $extractor;
    }
  }

  protected function extractGeneratorYml()
  {
    $generator = sprintf('%s/config/generator.yml', $this->pluginPath);
    if (file_exists($generator))
    {
      $yamlExtractor = new sfI18nYamlGeneratorExtractor();
      $this->updateMessages($yamlExtractor->extract(file_get_contents($generator)));
    }
  }

  protected function extractValidateYml()
  {
    $validateFiles = glob($this->pluginPath.'/validate/*.yml');
    if (is_array($validateFiles))
    {
      foreach ($validateFiles as $validateFile)
      {
        $yamlExtractor = new sfI18nYamlValidateExtractor();
        $this->updateMessages($yamlExtractor->extract(file_get_contents($validateFile)));
      }
    }
  }
}
