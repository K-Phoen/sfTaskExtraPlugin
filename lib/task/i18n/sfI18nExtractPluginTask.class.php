<?php

class sfI18nExtractPluginTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('plugin', sfCommandArgument::REQUIRED, 'The plugin name'),
      new sfCommandArgument('culture', sfCommandArgument::REQUIRED, 'The target culture'),
    ));

    $this->addOptions(array(
      new sfCommandOption('display-new', null, sfCommandOption::PARAMETER_NONE, 'Output all new found strings'),
      new sfCommandOption('display-old', null, sfCommandOption::PARAMETER_NONE, 'Output all old strings'),
      new sfCommandOption('auto-save', null, sfCommandOption::PARAMETER_NONE, 'Save the new strings'),
      new sfCommandOption('auto-delete', null, sfCommandOption::PARAMETER_NONE, 'Delete old strings'),
    ));

    $this->namespace = 'i18n';
    $this->name = 'extract-plugin';
    $this->briefDescription = 'Extracts i18n strings from php files';

    $this->detailedDescription = <<<EOF
The [i18n:extract|INFO] task extracts i18n strings from your project files
for the given plugin and target culture:

  [./symfony i18n:extract-plugin myPlugin fr|INFO]

By default, the task only displays the number of new and old strings
it found in the given plugin.

If you want to display the new strings, use the [--display-new|COMMENT] option:

  [./symfony i18n:extract-plugin --display-new myPlugin fr|INFO]

To save them in the i18n message catalogue, use the [--auto-save|COMMENT] option:

  [./symfony i18n:extract-plugin --auto-save myPlugin fr|INFO]

If you want to display strings that are present in the i18n messages
catalogue but are not found in the plugin, use the
[--display-old|COMMENT] option:

  [./symfony i18n:extract-plugin --display-old myPlugin fr|INFO]

To automatically delete old strings, use the [--auto-delete|COMMENT] but
be careful, especially if you have translations for plugins as they will
appear as old strings but they are not:

  [./symfony i18n:extract-plugin --auto-delete myPlugin fr|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  public function execute($arguments = array(), $options = array())
  {
    $this->logSection('i18n', sprintf('extracting i18n strings for the "%s" plugin', $arguments['plugin']));

    // create a dummy configuration
    $this->configuration = $this->createConfiguration($this->getFirstApplication(), null);

    // get i18n configuration from factories.yml
    $config = sfFactoryConfigHandler::getConfiguration($this->configuration->getConfigPaths('config/factories.yml'));

    $class = $config['i18n']['class'];
    $params = $config['i18n']['param'];
    unset($params['cache']);

    $extract = new sfI18nPluginExtractAll(
      new $class($this->configuration, new sfNoCache(), $params),
      $arguments['culture'],
      array_merge($arguments, $options),
      $this->configuration->getPluginPaths()
    );
    $extract->extract();

    $this->logSection('i18n', sprintf('found "%d" new i18n strings', count($extract->getNewMessages())));
    $this->logSection('i18n', sprintf('found "%d" old i18n strings', count($extract->getOldMessages())));

    if ($options['display-new'])
    {
      $this->logSection('i18n', sprintf('display new i18n strings', count($extract->getOldMessages())));
      foreach ($extract->getNewMessages() as $message)
      {
        $this->log('               '.$message."\n");
      }
    }

    if ($options['auto-save'])
    {
      $this->logSection('i18n', 'saving new i18n strings');

      $extract->saveNewMessages();
    }

    if ($options['display-old'])
    {
      $this->logSection('i18n', sprintf('display old i18n strings', count($extract->getOldMessages())));
      foreach ($extract->getOldMessages() as $message)
      {
        $this->log('               '.$message."\n");
      }
    }

    if ($options['auto-delete'])
    {
      $this->logSection('i18n', 'deleting old i18n strings');

      $extract->deleteOldMessages();
    }
  }
}
