<?php
/**
 * $Id: BasePermission.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class BasePermission extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_permissions');

        $this->hasColumn('name', 'string', 50, array ('unique' => true));
    }

    public function setUp()
    {
        $this->hasMany('Group as groups', array('refClass' => 'GroupPermission', 'local' => 'permission_id', 'foreign' => 'group_id'));
        $this->hasMany('User as users', array('refClass' => 'UserPermission', 'local' => 'permission_id', 'foreign' => 'user_id'));
    }
}
