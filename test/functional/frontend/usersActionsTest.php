<?php

// list of summits actions
$module_actions_list = array('edit',
                             'login',
                             'logout',
                             'lostPassword',
                             'messageResetPassword',
                             'messageSignupPassword',
                             'secure',
                             'setCultureInterface',
                             'signUp',
                             'view');
$module_name = 'users';

// include basic test and initiate test functionalities
include(dirname(__FILE__).'/../../bootstrap/BasicfunctionalTests.php');