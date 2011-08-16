<?php

include dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new sfTaskExtraLimeTest(3);
$t->setProjectConfiguration($configuration);

// lets create the extractor object
$culture = 'fr';
$i18n = $t->getI18n($culture, 'frontend');

$extractor = new sfI18nExtractAll($i18n, $culture, array('plugins' => array('i18nPlugin')));
$extractor->extract();


// begin: tests

$t->messages_ok(
  $extractor->getAllSeenMessages(),
  array(
    // from i18nPlugin plugin forms: FooI18nForm
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

    // from i18nPlugin plugin templates: indexSuccess.php
    'Hello world !',
    'The answer is 42',

    // from i18nPlugin plugin actions
    'Set in "index" action...but in the actions.class.php file',

    // from i18nPlugin plugin lib
    'Set in "index" action',
    'Set in "fooComponent" component',


    // app global layout
    'in the global layout',

    // app module: action
    'Set in "index" action... in the i18nModule of the frontend app',
    // app module: template
    'Hello (again) world !',

    // app lib: form
    'Please, fill this field ...',
    'Cool Field',
  ),
  '->getAllSeenMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getCurrentMessages(),
  array(
    'Title',
    'Deleted message',

    // as i18n folders in plugins are also considered as sources
    'Required.',
    'Set in "fooComponent" component',
    'This message does not exist anymore',
  ),
  '->getCurrentMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getOldMessages(),
  array(
    'Deleted message',

    // as i18n folders in plugins are also considered as sources
    'This message does not exist anymore',
  ),
  '->getOldMessages() returns %msg_total% messages.'
);
