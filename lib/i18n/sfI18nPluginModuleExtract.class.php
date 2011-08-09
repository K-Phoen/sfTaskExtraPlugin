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
  protected $moduleDir;

  /**
   * Configures the current extract object.
   */
  public function configure()
  {
    foreach (array('plugin', 'module') as $param)
    {
      if (empty($this->parameters[$param]))
      {
        throw new sfException(sprintf('You must give a "%s" parameter when extracting for a plugin.', $param));
      }
    }

    $this->moduleDir = sprintf('%s/%s/modules/%s',
      sfConfig::get('sf_plugins_dir'), $this->parameters['plugin'], $this->parameters['module']
    );
  }

  /**
   * Extracts i18n strings in all the possible locations of the module.
   * It looks:
   * * in actions/
   * * in lib/
   * * in templates/
   */
  public function extract()
  {
    // Extract from PHP files to find __() calls in actions/ lib/ and templates/ directories
    $this->extractFromPhpFiles(array(
      $this->moduleDir.'/actions',
      $this->moduleDir.'/lib',
      $this->moduleDir.'/templates',
    ));

    // Extract from generator.yml files
    $generator = $this->moduleDir.'/config/generator.yml';
    if (file_exists($generator))
    {
      $yamlExtractor = new sfI18nYamlGeneratorExtractor();
      $this->updateMessages($yamlExtractor->extract(file_get_contents($generator)));
    }

    // Extract from validate/*.yml files
    $validateFiles = glob($this->moduleDir.'/validate/*.yml');
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
