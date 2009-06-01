<?php
/**
 * $Id: BaseParking.class.php 2138 2007-10-22 12:03:24Z fvanderbiest $
 */

class BaseParking extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('parkings');

        $this->hasColumn('public_transportation_rating', 'smallint', 1);
        $this->hasColumn('public_transportation_types', 'string', null); // array
        $this->hasColumn('snow_clearance_rating', 'smallint', 1); 
        $this->hasColumn('lowest_elevation', 'smallint', 4); 
    }

    public function setUp()
    {
        $this->hasMany('ParkingI18n as ParkingI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('ParkingI18n', 'culture');
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
