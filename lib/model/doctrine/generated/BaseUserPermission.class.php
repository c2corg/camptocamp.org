<?php
/**
 * $Id: BaseUserPermission.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class BaseUserPermission extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_users_permissions');

        $this->hasColumn('user_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('permission_id', 'integer', 10, array('primary' => true));
    }
    
    public function setUp()
    {
        $this->hasOne('User', 'UserPermission.user_id');
        $this->hasOne('Permission', 'UserPermission.permission_id');
    }
}
