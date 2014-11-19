<?php
/**
 * $Id: BaseOutingArchive.class.php 2046 2007-10-11 19:17:24Z alex $
 */

class BaseOutingArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_outings_archives');

        $this->hasColumn('outing_archive_id', 'integer', 11);
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
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
