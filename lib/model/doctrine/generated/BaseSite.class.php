<?php
/**
 * $Id: BaseSite.class.php 2138 2007-10-22 12:03:24Z fvanderbiest $
 */

class BaseSite extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('sites');

        $this->hasColumn('routes_quantity', 'smallint', 4); 
        $this->hasColumn('max_rating', 'smallint', 2); 
        $this->hasColumn('min_rating', 'smallint', 2); 
        $this->hasColumn('mean_rating', 'smallint', 2); 
        $this->hasColumn('max_height', 'smallint', 4); 
        $this->hasColumn('min_height', 'smallint', 4); 
        $this->hasColumn('mean_height', 'smallint', 4); 
        $this->hasColumn('equipment_rating', 'smallint', 1); 
        $this->hasColumn('climbing_styles', 'string', null); // array in DB but Doctrine sees it as a string
        $this->hasColumn('rock_types', 'string', null); // array 
        $this->hasColumn('site_types', 'string', null); // array 
        $this->hasColumn('children_proof', 'smallint', 1); 
        $this->hasColumn('rain_proof', 'smallint', 1); 
        $this->hasColumn('facings', 'string', null); // array
        $this->hasColumn('best_periods', 'string', null); // array
        $this->hasColumn('v4_id', 'smallint', 5);
        $this->hasColumn('v4_type', 'string', 4);
    }

    public function setUp()
    {
        $this->hasMany('SiteI18n as SiteI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('SiteI18n', 'culture');
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
