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
        $this->hasMany('Association as MainAssociation', array('local' => 'main_id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'linked_id', 'foreign' => 'main_id'));
        $this->hasMany('GeoAssociation as MainGeoassociations', array('local' => 'main_id', 'foreign' => 'main_id')); 
        $this->hasMany('GeoAssociation as LinkedGeoassociations', array('local' => 'linked_id', 'foreign' => 'main_id')); 
        $this->hasMany('Document as MainDocument', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Document as LinkedDocument', array('local' => 'linked_id', 'foreign' => 'id'));
        $this->hasMany('Summit as Summit', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('SummitI18n as SummitI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Route as Route', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('RouteI18n as RouteI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Parking as Parking', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('ParkingI18n as ParkingI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Hut as Hut', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('HutI18n as HutI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Site as Site', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('SiteI18n as SiteI18n', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Article as Article', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('Outing as Outing', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('User as User', array('local' => 'main_id', 'foreign' => 'id'));
        $this->hasMany('UserI18n as UserI18n', array('local' => 'main_id', 'foreign' => 'id'));
    }
}
