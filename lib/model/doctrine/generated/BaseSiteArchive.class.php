<?php
/**
 * $Id: BaseSiteArchive.class.php 1971 2007-10-03 17:43:34Z alex $
 */

class BaseSiteArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_sites_archives');

        $this->hasColumn('site_archive_id', 'integer', 11);
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
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
