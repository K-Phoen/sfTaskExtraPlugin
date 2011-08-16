<?php

include dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new sfTaskExtraLimeTest(3);
$t->setProjectConfiguration($configuration);

// lets create the extractor object
$culture = 'fr';
$i18n = $t->getI18n($culture, 'frontend');

$extractor = new sfI18nFormExtract($i18n, $culture, array('form' => 'FooI18nForm'));
$extractor->extract();


// begin: tests

$t->messages_ok(
  $extractor->getAllSeenMessages(),
  array(
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
  ),
  '->getAllSeenMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getCurrentMessages(),
  array(
    'Title',
    'Required.',
    'Set in "fooComponent" component',

    // from the i18n dir (deleted messages)
    'This message does not exist anymore',
    'Deleted message',
  ),
  '->getCurrentMessages() returns %msg_total% messages.'
);
$t->messages_ok(
  $extractor->getOldMessages(),
  array(
    'Deleted message',
    'Set in "fooComponent" component',
    'This message does not exist anymore',
  ),
  '->getOldMessages() returns nothing.'
);
