<?php

include dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new sfTaskExtraLimeTest(3);
$t->setProjectConfiguration($configuration);

// lets create the extractor object
$culture = 'fr';
$i18n = $t->getI18n($culture, 'frontend');

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

    // from actions
    'Set in "index" action...but in the actions.class.php file',

    // from lib
    'Set in "index" action',
    'Set in "fooComponent" component',
  ),
  '->getAllSeenMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getCurrentMessages(),
  array(
    'Required.', // from the plugin i18n dir
    'Set in "fooComponent" component',

    // from the i18n dir (deleted messages)
    'This message does not exist anymore',
  ),
  '->getCurrentMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getOldMessages(),
  array(
    'This message does not exist anymore',
  ),
  '->getOldMessages() returns %msg_total% messages.'
);
