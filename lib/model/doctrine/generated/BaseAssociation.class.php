<?php
/**
 * $Id$
 */
class BaseAssociation extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_documents_associations');

        $this->hasColumn('main_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('linked_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('type', 'string', 2); // sr, ro ... no need to declare that it is a foreign key.
    }
    
    public function setUp()
    {
        $this->hasOne('Document as Main', 'Association.main_id');
        $this->hasOne('Document as Linked', 'Association.linked_id');
        // dangerous but more performant than fetching on documents view for 'sr' association type:
        $this->hasMany('Summit as Summit', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Route as Route', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Parking as Parking', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Hut as Hut', array('local' => 'main_id', 'foreign' => 'id'));
    }
}
