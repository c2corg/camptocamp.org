<?php
 
include(dirname(__FILE__).'/../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../apps/frontend/lib/Punbb.class.php');
 
$t = new lime_test(1, new lime_output_color());

$t->diag('test only the symfony pubb class bridget functions');
