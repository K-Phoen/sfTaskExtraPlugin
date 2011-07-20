<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage i18n
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfI18nModuleExtract.class.php 31248 2010-10-26 13:54:12Z fabien $
 */
class sfI18nPluginModuleExtract extends sfI18nModuleExtract
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

    if (!isset($this->parameters['module']))
    {
      throw new sfException('You must give a "module" parameter when extracting for a module.');
    }

    $this->plugin = $this->parameters['plugin'];
    $this->pluginPath = sprintf('%s/%s', sfConfig::get('sf_plugins_dir'), $this->plugin);
    $this->module = $this->parameters['module'];
  }

  /**
   * Extracts i18n strings in all the possible locations of the module.
   * It looks:
   * * in actions/
   * * in lib/
   * * in templates/
   *
   *
   * This class must be implemented by subclasses.
   */
  public function extract()
  {
    // Extract from PHP files to find __() calls in actions/ lib/ and templates/ directories
    $moduleDir = sprintf('%s/modules/%s', $this->pluginPath, $this->module);

    $this->extractFromPhpFiles(array(
      $moduleDir.'/actions',
      $moduleDir.'/lib',
      $moduleDir.'/templates',
    ));

    // Extract from generator.yml files
    $generator = $moduleDir.'/config/generator.yml';
    if (file_exists($generator))
    {
      $yamlExtractor = new sfI18nYamlGeneratorExtractor();
      $this->updateMessages($yamlExtractor->extract(file_get_contents($generator)));
    }

    // Extract from validate/*.yml files
    $validateFiles = glob($moduleDir.'/validate/*.yml');
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
