<?php
/**
 * $Id: BaseAreaArchive.class.php 1640 2007-09-13 13:55:17Z fvanderbiest $
 */

class BaseAreaArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_areas_archives');

        $this->hasColumn('area_archive_id', 'integer', 11);
        $this->hasColumn('area_type', 'smallint', 1);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
