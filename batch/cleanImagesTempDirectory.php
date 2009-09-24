<?php
/**
 * Batch that removes too old files from images temp directory
 * Must be lauched by a cron
 */

$expire_time = 2; // files older than expire_time hours will be deleted

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

$temp_dir = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR .
            sfConfig::get('app_images_temp_directory_name') . DIRECTORY_SEPARATOR;

//echo "$temp_dir";

if ($handle = opendir($temp_dir))
{
    while (false !== ($file = readdir($handle)))
    {
        $file_creation = filectime($temp_dir.$file);
        $file_age = time() - $file_creation;

        if (($file != '.') && ($file_age > ($expire_time * 3600)))
        {
            unlink($temp_dir.$file);
        }
    }
}
?>
