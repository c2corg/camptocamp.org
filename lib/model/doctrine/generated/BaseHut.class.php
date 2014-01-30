<?php
/**
 * $Id: BaseHut.class.php 2138 2007-10-22 12:03:24Z fvanderbiest $
 */

class BaseHut extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
 
        $this->setTableName('huts');

        $this->hasColumn('shelter_type', 'integer', 1); 
        $this->hasColumn('is_staffed', 'boolean', null);
        $this->hasColumn('phone', 'string', 50);
        $this->hasColumn('url', 'string', 255);
        $this->hasColumn('staffed_capacity', 'smallint', 3); 
        $this->hasColumn('unstaffed_capacity', 'smallint', 2); 
        $this->hasColumn('has_unstaffed_matress', 'smallint', 1);
        $this->hasColumn('has_unstaffed_blanket', 'smallint', 1);
        $this->hasColumn('has_unstaffed_gas', 'smallint', 1);
        $this->hasColumn('has_unstaffed_wood', 'smallint', 1);
        $this->hasColumn('activities', 'string', null); // array
    }

    public function setUp()
    {
        $this->hasMany('HutI18n as HutI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('HutI18n', 'culture');
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
} 
