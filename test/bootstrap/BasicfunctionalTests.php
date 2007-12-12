<?php

include(dirname(__FILE__).'/functional.php');

// create a new test browser
$browser = new sfTestBrowser();
$browser->initialize();

// basic test for all the summits actions
foreach($module_actions_list as $action)
{
    $browser->
        get('/' . $module_name . '/' . $action)->
        isStatusCode(200)->
        isRequestParameter('module', $module_name)->
        isRequestParameter('action', $action)
    ;
}