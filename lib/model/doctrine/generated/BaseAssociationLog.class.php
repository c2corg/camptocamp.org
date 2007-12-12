<?php
/**
 * $Id:$
 */

class BaseAssociationLog extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_associations_log');

        $this->hasColumn('associations_log_id', 'integer', 11, array('primary', 'seq' => 'associations_log'));
        $this->hasColumn('main_id', 'integer', 11);
        $this->hasColumn('linked_id', 'integer', 11);
        $this->hasColumn('type', 'string', 2); 
        $this->hasColumn('user_id', 'integer', 11);
        $this->hasColumn('is_creation', 'boolean', null);
        $this->hasColumn('written_at', 'timestamp', null); // update managed by postgres
    }

    public function setUp()
    {   
        $this->hasMany('DocumentI18n as mainI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('DocumentI18n as linkedI18n', array('local' => 'linked_id', 'foreign' => 'id'));
        
        $this->hasOne('UserPrivateData as user_private_data', 'AssociationLog.user_id');
    }
}
