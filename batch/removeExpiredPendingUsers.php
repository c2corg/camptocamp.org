<?php
/**
 * Batch that removes too old pending users
 * Must be lauched by a cron
 *
 * @version $Id: removeExpiredPendingUsers.php 2365 2007-11-19 14:32:23Z alex $
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

$users_id = User::getOutOfDatePendingUserIds();

if ($users_id)
{
    $temp = array();
    foreach ($users_id as $id)
    {
        $temp[] = '?';
    }
    $where = 'u.id IN (' . implode(', ', $temp) . ')';
	
    $users = Doctrine_Query::create()
                           ->from('User u, u.private_data')
                           ->where($where, $users_id)
                           ->execute();
    
    // delete them
    foreach ($users as $user)
    {
        $id = $user->getId();
        // delete the document user (versions, metadata, i18narchives, archives)
        $user->doDelete($id);
        
        // delete user private data
        $user->private_data->delete();
        
        // delete user group relations
        $user->UserGroup->delete();

        echo "Removed expired pending user #$id\n";
    }
}
