<?php
/**
 * $Id: BaseParkingArchive.class.php 1354 2007-08-21 13:11:21Z alex $
 */

class BaseParkingArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_parkings_archives');

        $this->hasColumn('parking_archive_id', 'integer', 11);
        $this->hasColumn('public_transportation_rating', 'smallint', 1);
        $this->hasColumn('snow_clearance_rating', 'smallint', 1);
        $this->hasColumn('lowest_elevation', 'smallint', 4);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
