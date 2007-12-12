<?php
/**
 * $Id: BaseUserGroup.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class BaseUserGroup extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_users_groups');
        
        $this->hasColumn('user_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('group_id', 'integer', 10, array('primary' => true));
    }
    
    public function setUp()
    {
        $this->hasOne('User', 'UserGroup.user_id');
        $this->hasOne('Group', 'UserGroup.group_id');
    }
}
