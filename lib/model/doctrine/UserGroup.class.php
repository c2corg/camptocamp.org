<?php
/*
 * Edit this file to customise your model class
 * $Id: UserGroup.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class UserGroup extends BaseUserGroup
{
    public static function findByUserIdAndGroupName($user_id, $group_name)
    {
        return Doctrine_Query::create()
                             ->from('UserGroup')
                             ->where('UserGroup.Group.name = ? AND UserGroup.User.id = ?',
                                     array($group_name, $user_id))
                             ->limit(1)
                             ->execute()
                             ->getFirst();
    }
}
