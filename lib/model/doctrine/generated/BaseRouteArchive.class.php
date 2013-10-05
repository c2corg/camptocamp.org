<?php
/**
 * $Id: BaseRouteArchive.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseRouteArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_routes_archives');

        $this->hasColumn('route_archive_id', 'integer', 11);
        $this->hasColumn('facing', 'smallint', null);
        $this->hasColumn('activities', 'string', null); // array in DB but Doctrine sees it as a string
        $this->hasColumn('height_diff_up', 'smallint', null);
        $this->hasColumn('height_diff_down', 'smallint', null);
        $this->hasColumn('route_type', 'smallint', null);
        $this->hasColumn('route_length', 'integer', 5);
        $this->hasColumn('min_elevation', 'smallint', null);
        $this->hasColumn('max_elevation', 'smallint', null);
        $this->hasColumn('duration', 'smallint', null);
        $this->hasColumn('slope', 'string', 100);
        $this->hasColumn('difficulties_height', 'smallint', null);
        $this->hasColumn('configuration', 'string', null); // array
        $this->hasColumn('global_rating', 'smallint', null);
        $this->hasColumn('engagement_rating', 'smallint', null);
        $this->hasColumn('objective_risk_rating', 'smallin', null);
        $this->hasColumn('equipment_rating', 'smallint', null);
        $this->hasColumn('is_on_glacier', 'boolean', null);
        $this->hasColumn('sub_activities', 'string', null); // array
        $this->hasColumn('toponeige_technical_rating', 'smallint', null);
        $this->hasColumn('toponeige_exposition_rating', 'smallint', null);
        $this->hasColumn('labande_ski_rating', 'smallint', null);
        $this->hasColumn('labande_global_rating', 'smallint', null);
        $this->hasColumn('ice_rating', 'smallint', null);
        $this->hasColumn('mixed_rating', 'smallint', null);
        $this->hasColumn('rock_free_rating', 'smallint', null);
        $this->hasColumn('rock_required_rating', 'smallint', null);
        $this->hasColumn('aid_rating', 'smallint', null);
        $this->hasColumn('rock_exposition_rating', 'smallint', null);
        $this->hasColumn('hiking_rating', 'smallint', null);
        $this->hasColumn('snowshoeing_rating', 'smallint', null);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
