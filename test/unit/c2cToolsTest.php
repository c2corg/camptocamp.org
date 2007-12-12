<?php

include(dirname(__FILE__).'/../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../apps/frontend/lib/c2cTools.class.php');

$t = new lime_test(2, new lime_output_color());

// test all things that c2ctools can do
$t->can_ok(c2cTools, 'log', 'c2cTools can log');

$t->todo('test the log method... but dont know how...');
// $t->diag('::log');
// todo
// - if log actived, log must be written
// - if not, no log must be written
// - log message must start with {c2c}
// - log message must be an info type