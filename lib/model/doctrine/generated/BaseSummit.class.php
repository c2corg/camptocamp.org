<?php
/**
 * $Id: BaseSummit.class.php 2138 2007-10-22 12:03:24Z fvanderbiest $
 */

class BaseSummit extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('summits');

        $this->hasColumn('summit_type', 'smallint', null);
        $this->hasColumn('maps_info', 'string', 300);
        $this->hasColumn('v4_id', 'smallint', 5);
    }

    public function setUp()
    {
        $this->hasMany('SummitI18n as SummitI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('SummitI18n', 'culture');
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
