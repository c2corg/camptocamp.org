<?php

// list of summits actions
$module_actions_list = array('view', 'preview', 'distancefrom', 'edit');
$module_name = 'summits';

// include basic test and initiate test functionalities
include(dirname(__FILE__).'/../../bootstrap/BasicfunctionalTests.php');

/**
 * test create action
 * culture,lon,lat, culture, name, description, rev_comment, rev_is_minor
 */ 
$action = 'edit';
$browser->
    post('/' . $module_name . '/' . $action, array('culture' => 'fr', 
                                             'lon' => '34.44', 
                                             'lat' => '22.4858',
                                             'name' => 'super nom',
                                             'desciption' => 'super description'))->
    isRedirected()->
    followRedirect()->  
    isStatusCode(200)->
    isRequestParameter('module', $module_name)->
    isRequestParameter('action', $action)
;