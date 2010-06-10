<?php
/**
 * $Id: BaseUser.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseUser extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('users');

        $this->hasColumn('v4_id', 'smallint', 5);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('category', 'smallint', 1);
    }

    public function setUp()
    {
        $this->ownsOne('UserPrivateData as private_data', array('local' => 'id', 'foreign' => 'id'));
        $this->hasMany('UserI18n as UserI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('UserI18n', 'culture');
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id')); 
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));

        // credentials management
        $this->hasMany('Group as groups', array('local' => 'user_id', 'foreign' => 'group_id', 'refClass' => 'UserGroup'));
        $this->hasMany('Permission as permissions', array('refClass' => 'UserPermission', 'local' => 'user_id', 'foreign' => 'permission_id'));

        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));

    }
}
