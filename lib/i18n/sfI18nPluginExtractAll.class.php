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

    $options = $this->i18n->getOptions();
    $dirs = $this->i18n->isMessageSourceFileBased($options['source']) ? array(sprintf('%s/i18n', $this->pluginPath)) : null;
    $this->i18n->setMessageSource($dirs, $this->culture);
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

    // Extract from generator.yml files
    $this->extractGeneratorYml();

    // Extract from validate/*.yml files
    $this->extractValidateYml();
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
