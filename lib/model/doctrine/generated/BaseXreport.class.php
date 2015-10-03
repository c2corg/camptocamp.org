<?php

class BaseXreport extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('xreports');

        $this->hasColumn('date', 'date', null);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('nb_participants', 'smallint', 3); 
        $this->hasColumn('nb_impacted', 'smallint', 3); 
        $this->hasColumn('severity', 'smallint', 1);
        $this->hasColumn('rescue', 'boolean', null);
        $this->hasColumn('event_type', 'string', null); // array
        $this->hasColumn('author_status', 'smallint', 1);
        $this->hasColumn('activity_rate', 'smallint', 1);
        $this->hasColumn('nb_outings', 'smallint', 1);
        $this->hasColumn('autonomy', 'smallint', 1);
        $this->hasColumn('age', 'smallint', 1);
        $this->hasColumn('gender', 'smallint', 1);
        $this->hasColumn('previous_injuries', 'smallint', 1);
   }

    public function setUp()
    {
        $this->hasMany('XreportI18n as XreportI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('XreportI18n', 'culture');
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
