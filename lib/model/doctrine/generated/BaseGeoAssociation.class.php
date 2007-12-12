<?php
/**
 * $Id$
 */
class BaseGeoAssociation extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_geo_associations');

        $this->hasColumn('main_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('linked_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('type', 'string', 2); 
    }
    
    public function setUp()
    {
        $this->hasOne('Document as Document', 'Association.main_id');
        // not dangerous:
        $this->hasOne('Document as Document', 'Association.linked_id');
        // dangerous, but maybe useful:
        //$this->hasOne('Area as Area', 'Association.linked_id'); // type = 'dr', 'dd', 'dc'
        //$this->hasOne('Map as Map', 'Association.linked_id'); // type = 'dm'
        // most dangerous : but better since it prevents one join with Area
        $this->hasMany('AreaI18n as AreaI18n', array('local' => 'linked_id', 'foreign' => 'id'));
    }
}
