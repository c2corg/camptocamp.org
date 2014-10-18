<?php
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

// other solution below
// Problem is that it doesn't use the cache (maybe not so important here)
*/

include(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP . 
             DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'templates'. 
             DIRECTORY_SEPARATOR.($mobile_version ? '_mobile_footer.php' : '_footer.php'));

?>
</div>
<?php
$punbb_file = basename($_SERVER['PHP_SELF']);

$sf_response->addJavascript('/static/js/jquery.min.js', 'first');
$sf_response->addJavascript('/static/js/feedback.js');
$sf_response->addJavascript('/static/js/queue.js');

// in order to optimize cache, we always include the following js (about 2k once compressed...)
//if (in_array($punbb_file, array('viewtopic.php', 'post.php', 'edit.php', 'message_send.php', 'message_list.php')))
//{
    $sf_response->addJavascript('/static/js/easy_bbcode.js');
    if (!$mobile_version)
    {
        $sf_response->addJavascript('/static/js/easy_bbcode_images.js');
    }
//}

if (!$mobile_version)
{
    $sf_response->addJavascript('/static/js/modal.js');
}

$debug = defined('PUN_DEBUG');
minify_include_body_javascripts(!$debug, $debug);

// tracker_forum_id is used to track the visited forums for analytics (see _tracker.php)
if ($punbb_file === 'viewtopic.php')
{
    $tracker_forum_id = $cur_topic['forum_id'];
}
else if ($punbb_file === 'viewforum.php')
{
    $tracker_forum_id = FORUM_FEED;
}

include(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . SF_APP .
             DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'templates'.
             DIRECTORY_SEPARATOR.'_tracker.php'); 
