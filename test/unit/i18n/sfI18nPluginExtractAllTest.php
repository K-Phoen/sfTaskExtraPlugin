<?php

include dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new sfTaskExtraLimeTest(3);

// lets create the extractor object
$culture = 'fr';
$app_conf = $configuration->getApplicationConfiguration('frontend', 'test', true, dirname(__FILE__).'/../../fixtures/project');
$config = sfFactoryConfigHandler::getConfiguration($app_conf->getConfigPaths('config/factories.yml'));

$class = $config['i18n']['class'];
$params = $config['i18n']['param'];
unset($params['cache']);

$i18n = new $class($app_conf, new sfNoCache(), $params);

$extractor = new sfI18nPluginExtractAll($i18n, $culture, array('plugin' => 'i18nPlugin'));
$extractor->extract();


// begin: tests

$t->messages_ok(
  $extractor->getAllSeenMessages(),
  array(
    // from forms: FooI18nForm
    'Required.',    // validator
    'Invalid.',
    'Title',        // widget label
    'Try to fill this field ...',
    '"%value%" is too long (%max_length% characters max).',
    '"%value%" is too short (%min_length% characters min).',
    'CSRF attack detected.',
    'Content',
    ' csrf token',
    'Oh! this validator is a very dumb one', // post validator

    // from templates: indexSuccess.php
    'Hello world !',
    'The answer is 42',

    // from actions: @TODO
    // from lib: @TODO
  ),
  '->getAllSeenMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getCurrentMessages(),
  array(
    'Required.', // from the plugin i18n dir
  ),
  '->getCurrentMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getOldMessages(),
  array(),
  '->getOldMessages() returns nothing.'
);
