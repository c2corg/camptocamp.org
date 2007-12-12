<?php
/**
 * $Id: BaseMap.class.php 2138 2007-10-22 12:03:24Z fvanderbiest $
 */

class BaseMap extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('maps');

        $this->hasColumn('editor', 'integer', null);
        $this->hasColumn('scale', 'integer', null);
        $this->hasColumn('code', 'string', 20);
    }

    public function setUp()
    {
        $this->hasMany('MapI18n as MapI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('MapI18n', 'culture');
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
