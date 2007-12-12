<?php
/**
 * $Id: BaseGroupPermission.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class BaseGroupPermission extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_groups_permissions');

        $this->hasColumn('group_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('permission_id', 'integer', 10, array('primary' => true));
    }
    
    public function setUp()
    {
        $this->hasOne('Group', 'GroupPermission.group_id');
        $this->hasOne('Permission', 'GroupPermission.permission_id');
    }
}
