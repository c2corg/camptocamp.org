<?php
/**
 * $Id: BaseOuting.class.php 2542 2007-12-21 19:07:08Z alex $
 */

class BaseOuting extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('outings');

        $this->hasColumn('date', 'date', null);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('height_diff_up', 'smallint', null);
        $this->hasColumn('height_diff_down', 'smallint', null);
        $this->hasColumn('outing_length', 'integer', 6);
        $this->hasColumn('min_elevation', 'smallint', 4);
        $this->hasColumn('max_elevation', 'smallint', 4);
        $this->hasColumn('partial_trip', 'boolean', null);
        $this->hasColumn('hut_status', 'smallint', 1);
        $this->hasColumn('frequentation_status', 'smallint', 1);
        $this->hasColumn('conditions_status', 'smallint', 1);
        $this->hasColumn('access_status', 'smallint', 1);
        $this->hasColumn('access_elevation', 'smallint', 4);
        $this->hasColumn('lift_status', 'smallint', 1);
        $this->hasColumn('glacier_status', 'smallint', 1);
        $this->hasColumn('up_snow_elevation', 'smallint', 4);
        $this->hasColumn('down_snow_elevation', 'smallint', 4);
        $this->hasColumn('track_status', 'smallint', 1);
        $this->hasColumn('outing_with_public_transportation', 'boolean', null);
        $this->hasColumn('avalanche_date', 'string', null); // array
        $this->hasColumn('v4_id', 'smallint', 5);
        $this->hasColumn('v4_app', 'string', 3);
    }

    public function setUp()
    {
        $this->hasMany('OutingI18n as OutingI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('OutingI18n', 'culture');
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
