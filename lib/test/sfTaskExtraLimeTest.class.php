<?php

/**
 * Tests symfony tasks.
 *
 * @package     sfTaskExtraPlugin
 * @subpackage  test
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id$
 */
class sfTaskExtraLimeTest extends lime_test
{
  protected $configuration;

  /**
   * Define the project configuration to use for the next tests.
   *
   * @param   ProjectConfiguration  $conf The configuration object.
   *
   * @author Kévin Gomez <contact@kevingomez.fr>
   */
  public function setProjectConfiguration(ProjectConfiguration $conf)
  {
    $this->configuration = $conf;
  }

  /**
   * Get the project configuration used for the tests
   *
   * @return ProjectConfiguration The configuration object.
   */
  public function getProjectConfiguration()
  {
    return $this->configuration;
  }

  /**
   * Factory method which creates a i18n class instance for the given language
   * and application.
   *
   * @param   string  $language The destination language (default to 'fr').
   * @param   string  $app      The application to use (default to 'frontend').
   *
   * @return object
   * @author Kévin Gomez <contact@kevingomez.fr>
   */
  public function getI18N($language='fr', $app='frontend')
  {
    $app_conf = $this->configuration->getApplicationConfiguration($app, 'test', true, dirname(__FILE__).'/../../test/fixtures/project');
    $config = sfFactoryConfigHandler::getConfiguration($app_conf->getConfigPaths('config/factories.yml'));

    $class = $config['i18n']['class'];
    $params = $config['i18n']['param'];
    unset($params['cache']);

    return new $class($app_conf, new sfNoCache(), $params);
  }

  /**
   * Executes a task and tests its success.
   *
   * @param   string  $taskClass
   * @param   array   $arguments
   * @param   array   $options
   * @param   boolean $boolean
   *
   * @return  boolean
   */
  public function task_ok($taskClass, array $arguments = array(), array $options = array(), $boolean = true, $message = null)
  {
    if (null === $message)
    {
      $message = sprintf('"%s" execution %s', $taskClass, $boolean ? 'succeeded' : 'failed');
    }

    chdir(dirname(__FILE__).'/../../test/fixtures/project');

    $task = new $taskClass($this->configuration->getEventDispatcher(), new sfFormatter());
    $task->setConfiguration($this->configuration);
    try
    {
      $ok = $boolean === $task->run($arguments, $options) ? false : true;
    }
    catch (Exception $e)
    {
      $ok = $boolean === false;
    }

    $this->ok($ok, $message);

    if (isset($e) && !$ok)
    {
      $this->diag('    '.$e->getMessage());
    }

    return $ok;
  }

  /**
   * Test if the received i18n strings match the expected ones.
   *
   * @param array $got_messages       Array of i18n string, as return by an extractor object.
   *                                  Only its values will be tested.
   * @param array $expected_messages  Array of expected i18n strings.
   */
  public function messages_ok($got_messages, $expected_messages, $message='')
  {
    $got_messages = array_values($got_messages);

    sort($got_messages);
    sort($expected_messages);

    $this->is(
      $got_messages, $expected_messages,
      strtr($message, array('%msg_total%' => count($expected_messages)))
    );
  }
}
