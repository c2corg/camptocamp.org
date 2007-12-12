<?php
/**
 * $Id: BaseArea.class.php 2138 2007-10-22 12:03:24Z fvanderbiest $
 */

class BaseArea extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('areas');

        $this->hasColumn('area_type', 'smallint', 1);
    }

    public function setUp()
    {
        $this->hasMany('AreaI18n as AreaI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('AreaI18n', 'culture');
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id')); 
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
