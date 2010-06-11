<?php
$mobile_version = c2cTools::mobileVersion();

if (!defined('SF_ROOT_DIR'))
{
    // include config vars that are needed for symfony
    define('SF_ROOT_DIR',    realpath(dirname(__file__).'/../../../..'));
    define('SF_APP',         'frontend');
    define('SF_ENVIRONMENT', 'prod');
    define('SF_DEBUG',       false);

    require_once SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . 
             DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
}

/*
$context = sfContext::getInstance();

// set the relative root to null, then the links will be OK.
$context->getRequest()->setRelativeUrlRoot('');


// get the output HTML for the 'bottom' fictive action from the 'common' module in symfony:
echo $context->getController()->getPresentationFor('common', 'bottom');
// this is a trick because include_partial('common/footer'); does not work here.

// other solution :*/

include(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . 
             DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'templates'. 
             DIRECTORY_SEPARATOR.($mobile_version ? '_mobile_footer.php' : '_footer.php'));

if (in_array(basename($_SERVER['PHP_SELF']), array('viewtopic.php', 'post.php', 'edit.php', 'message_send.php', 'message_list.php')))
{
    $sf_response->addJavascript(PUN_STATIC_URL . '/static/js/easy_bbcode.js');
}
$sf_response->addJavascript(PUN_STATIC_URL . '/static/js/modalbox.js');

$debug = defined('PUN_DEBUG');
minify_include_body_javascripts(!$debug, $debug);

include(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP .
             DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'templates'.
             DIRECTORY_SEPARATOR.'_tracker.php');             
