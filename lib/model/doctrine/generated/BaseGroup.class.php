<?php
/**
 * $Id: BaseGroup.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class BaseGroup extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_groups');

        $this->hasColumn('name', 'string', 50, array ('unique' => true));
    }

    public function setUp()
    {
        $this->hasMany('Permission as permissions', array('refClass' => 'GroupPermission', 'local' => 'group_id', 'foreign' => 'permission_id'));
        $this->hasMany('User as users', array('refClass' => 'UserGroup', 'local' => 'group_id', 'foreign' => 'user_id'));
    }
}
