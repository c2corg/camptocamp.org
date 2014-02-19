#!/usr/bin/php
<?php
/**
 * Batch that merges 2 users
 *
 * The first user will be merged into the second one, and then the first user will be deleted
 */

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'frontend');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// needed for doctrine connection to work
sfContext::getInstance();

if ($argc != 3 
    || in_array($argv[1], array('--help', '-help', '-h', '-?'))
    || !is_numeric($argv[1])
    || !is_numeric($argv[2]))
{
    echo "Usage: $argv[0] <user_id_to_wipe> <user_id_to_remain>\n";
    exit;
}

$userid_to_wipe = $argv[1];
$userid_to_remain = $argv[2];

// Check that inputs are ok
if ($userid_to_remain == $userid_to_wipe)
    die("User to wipe and user to remain have the same id. Exiting...\n");

$user_to_wipe_document = Document::find('User', $userid_to_wipe, array('is_protected', 'module'));
if (!$user_to_wipe_document || ($user_to_wipe_document->get('module') != 'users'))
    die("User to wipe is not a valid user. Exiting...\n");
if ($user_to_wipe_document->get('is_protected'))
    die("User to wipe is protected. Exiting...\n");

$user_to_remain_document = Document::find('User', $userid_to_remain, array('is_protected', 'module'));
if (!$user_to_remain_document || ($user_to_remain_document->get('module') != 'users'))
    die("User to remain is not a valid user. Exiting...\n");
if ($user_to_remain_document->get('is_protected'))
    die("User to remain is protected. Exiting...\n");

// retrieve forum names and emails
$private_data = Doctrine_Query::create()
    ->select('pd.username, pd.email')
    ->from('UserPrivateData pd')
    ->where('id = ?', array($userid_to_wipe))
    ->execute()
    ->getFirst();
$username_to_wipe = addslashes($private_data->get('username'));
$email_to_wipe = $private_data->get('email');

$private_data = Doctrine_Query::create()
    ->select('pd.username')
    ->from('UserPrivateData pd')
    ->where('id = ?', array($userid_to_remain))
    ->execute()
    ->getFirst();
$username_to_remain = addslashes($private_data->get('username'));
$email_to_remain = $private_data->get('email');

// display infos
echo "User to wipe is $userid_to_wipe ($username_to_wipe <$email_to_wipe>)\n";
echo "User to remain is $userid_to_remain ($username_to_remain <$email_to_remain>)\n";

echo "Are you sure you want to merge user $userid_to_wipe into $userid_to_remain? [y/N]: ";

flush();
ob_flush();

if (trim(fgets(STDIN)) !== 'y')
{
    echo "Aborting...\n";
    exit(0);
}

// Everything seems ok for merging...
$conn = sfDoctrine::Connection();
try
{
    $conn->beginTransaction();

    // forum part inspired by http://punbb.org/download/plugins/AP_User_Merge.zip

    $conn->standaloneQuery("UPDATE punbb_forums SET last_poster='$username_to_remain' WHERE last_poster='$username_to_wipe'");

    $result = $conn->standaloneQuery("SELECT id, moderators FROM punbb_forums")->fetchAll();
    foreach ($result as $row)
    {
        $cur_moderators = ($row['moderators'] != '') ? unserialize($row['moderators']) : array();
        if (in_array($userid_to_wipe, $cur_moderators))
        {
            $username = array_search($userid_to_wipe, $cur_moderators);
            unset($cur_moderators[$username]);
            $cur_moderators[$username_to_remain] = $userid_to_remain;
            ksort($cur_moderators);
            $cur_moderators = (!empty($cur_moderators)) ? '\''.$db->escape(serialize($cur_moderators)).'\'' : 'NULL';
            $conn->standaloneQuery("UPDATE punbb_forums SET moderators=".$cur_moderators." WHERE id=".$cur_forum['id']);
        }
    }

    //$conn->standaloneQuery("UPDATE punbb_online SET user_id='$userid_to_remain', ident='$username_to_remain' WHERE user_id='$userid_to_wipe'");
    $conn->standaloneQuery("DELETE FROM punbb_online WHERE user_id='$userid_to_wipe'");

    $conn->standaloneQuery("UPDATE punbb_posts SET poster='$username_to_remain', poster_id='$userid_to_remain' WHERE poster_id='$userid_to_wipe'");
    $conn->standaloneQuery("UPDATE punbb_posts SET edited_by='$username_to_remain' WHERE edited_by='$username_to_wipe'");

    $conn->standaloneQuery("UPDATE punbb_reports SET reported_by='$userid_to_remain' WHERE reported_by='$userid_to_wipe'");
    $conn->standaloneQuery("UPDATE punbb_reports SET zapped_by='$userid_to_remain' WHERE zapped_by='$userid_to_wipe'");

    $conn->standaloneQuery("UPDATE punbb_subscriptions SET user_id='$userid_to_remain' WHERE user_id='$userid_to_wipe'");

    $conn->standaloneQuery("UPDATE punbb_topics SET poster='$username_to_remain' WHERE poster='$username_to_wipe'");
    $conn->standaloneQuery("UPDATE punbb_topics SET last_poster='$username_to_remain' WHERE last_poster='$username_to_wipe'");

    $conn->standaloneQuery("UPDATE punbb_users SET num_posts=num_posts + (SELECT num_posts FROM punbb_users WHERE id='$userid_to_wipe') WHERE id='$userid_to_remain'");
    $conn->standaloneQuery("UPDATE punbb_users SET last_post=(SELECT max(last_post) FROM punbb_users WHERE id='$userid_to_wipe' OR id ='$userid_to_remain') WHERE id ='$userid_to_remain'");


    // guidebook part

    // credit user to remain for all document changes
    Doctrine_Query::create()
        ->update('HistoryMetadata hm')
        ->set('hm.user_id', $userid_to_remain)
        ->where('hm.user_id = ?', $userid_to_wipe)
        ->execute();

    // credit user to remain for all document associations
    Doctrine_Query::create()
        ->update('AssociationLog al')
        ->set('al.user_id', $userid_to_remain)
        ->where('al.user_id = ?', $userid_to_wipe)
        ->execute();

    // replace each occurence of user to wipe in association logs
    // this might create some duplicates, but we don't care...
    Doctrine_Query::create()
        ->update('AssociationLog al')
        ->set('al.main_id', $userid_to_remain)
        ->where('al.main_id = ?', $userid_to_wipe)
        ->execute();
    Doctrine_Query::create()
        ->update('AssociationLog al')
        ->set('al.linked_id', $userid_to_remain)
        ->where('al.linked_id = ?', $userid_to_wipe)
        ->execute();

    // Remove associations with user to wipe if user to remain is also associated
    // TODO is that possible with DQL?
    $conn->standaloneQuery("DELETE FROM app_documents_associations
                            WHERE linked_id='$userid_to_wipe' AND main_id IN (
                            (SELECT main_id FROM app_documents_associations WHERE linked_id='$userid_to_remain')
                            INTERSECT (SELECT main_id FROM app_documents_associations WHERE linked_id='$userid_to_wipe'))");
    $conn->standaloneQuery("DELETE FROM app_documents_associations
                            WHERE main_id='$userid_to_wipe' AND linked_id IN (
                            (SELECT linked_id FROM app_documents_associations WHERE main_id='$userid_to_remain')
                            INTERSECT (SELECT linked_id FROM app_documents_associations WHERE main_id='$userid_to_wipe'))");
    // replace occurences of user to wipe in linked_id and main_id by user to remain
    Doctrine_Query::create()
        ->update('Association a')
        ->set('a.main_id', $userid_to_remain)
        ->where('a.main_id = ?', $userid_to_wipe)
        ->execute();
    Doctrine_Query::create()
        ->update('Association a')
        ->set('a.linked_id', $userid_to_remain)
        ->where('a.linked_id = ?', $userid_to_wipe)
        ->execute();

    // Same process for geo associations
    $conn->standaloneQuery("DELETE FROM app_geo_associations
                            WHERE linked_id='$userid_to_wipe' AND main_id IN (
                            (SELECT main_id FROM app_geo_associations WHERE linked_id='$userid_to_remain')
                            INTERSECT (SELECT main_id FROM app_geo_associations WHERE linked_id='$userid_to_wipe'))");
    $conn->standaloneQuery("DELETE FROM app_geo_associations
                            WHERE main_id='$userid_to_wipe' AND linked_id IN (
                            (SELECT linked_id FROM app_geo_associations WHERE main_id='$userid_to_remain')
                            INTERSECT (SELECT linked_id FROM app_geo_associations WHERE main_id='$userid_to_wipe'))");
    Doctrine_Query::create()
        ->update('GeoAssociation ga')
        ->set('ga.linked_id', $userid_to_remain)
        ->where('ga.linked_id = ?', $userid_to_wipe)
        ->execute();
    Doctrine_Query::create()
        ->update('GeoAssociation ga')
        ->set('ga.main_id', $userid_to_remain)
        ->where('ga.main_id = ?', $userid_to_wipe)
        ->execute();

    // Update groups membership
    $conn->standaloneQuery("DELETE FROM app_users_groups 
                            WHERE user_id='$userid_to_wipe' AND group_id IN (
                            (SELECT group_id FROM app_users_groups WHERE user_id='$userid_to_remain')
                            INTERSECT (SELECT group_id FROM app_users_groups WHERE user_id='$userid_to_wipe'))");
    Doctrine_Query::create()
        ->update('UserGroup ug')
        ->set('ug.user_id', $userid_to_remain)
        ->where('ug.user_id = ?', $userid_to_wipe)
        ->execute();

    // Update user permissions
    $conn->standaloneQuery("DELETE FROM app_users_permissions
                            WHERE user_id='$userid_to_wipe' AND permission_id IN (
                            (SELECT permission_id FROM app_users_permissions WHERE user_id='$userid_to_remain')
                            INTERSECT (SELECT permission_id FROM app_users_permissions WHERE user_id='$userid_to_wipe'))");
    Doctrine_Query::create()
        ->update('UserPermission up')
        ->set('up.user_id', $userid_to_remain)
        ->where('up.user_id = ?', $userid_to_wipe)
        ->execute();

    // Remove entries linked to user to wipe for remember keys
    Doctrine_Query::create()
        ->delete()
        ->from('RememberKey')
        ->addWhere("user_id = '$userid_to_wipe'")
        ->execute();

    // Remove all subscriptions from the old user
    Doctrine_Query::create()
        ->delete()
        ->from('Sympa')
        ->addWhere("user_subscriber='$email_to_wipe'")
        ->execute();

    // Remove User Document
    Document::doDelete($userid_to_wipe);

    // Remove private data
    Doctrine_Query::create()
        ->delete()
        ->from('UserPrivateData')
        ->addWhere("id = '$userid_to_wipe'")
        ->execute();

    $conn->commit();
}
catch (Exception $e)
{
    $conn->rollback();
    echo ("A problem occured during merging\n");
    throw $e;
}

// Clear cache - We cannot use function from c2cActions since it is protected
$cache_dir = sfConfig::get('sf_root_cache_dir') . '/frontend/*/template/*/all';
$cache_dir .= (sfConfig::get('sf_no_script_name')) ? '/' : '/*/';

//c2cActions::clearCache('users', $userid_to_wipe, false);
$toRemove[] = "users/*/id/$userid_to_wipe/*";
//c2cActions::clearCache('users', $userid_to_remain, false);
$toRemove[] = "users/*/id/$userid_to_remain/*";

// find all docs associated to user_id_to_remain and clear the cache
$associated_docs = Association::findAllAssociatedDocs($userid_to_remain, array('id', 'module'));
foreach ($associated_docs as $doc)
{
    //c2cActions::clearCache($doc['module'], $doc['id'], false, 'view');
    $toRemove[] = "{$doc['module']}/view/id/{$doc['id']}/*";
}

// find all geo associated docs
$associated_docs = GeoAssociation::findAllAssociatedDocs($userid_to_remain, array('id', 'module'));
foreach ($associated_docs as $doc)
{
    //c2cActions::clearCache($doc['module'], $doc['id'], false, 'view');
    $toRemove[] = "{$doc['module']}/view/id/{$doc['id']}/*";
}

foreach ($toRemove as $item)
{
    sfToolkit::clearGlob($cache_dir . $item);
}

echo "Users successfully merged\n";

