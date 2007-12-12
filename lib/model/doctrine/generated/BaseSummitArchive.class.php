<?php
/**
 * $Id: BaseSummitArchive.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseSummitArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_summits_archives');

        $this->hasColumn('summit_archive_id', 'integer', 11);
        $this->hasColumn('summit_type', 'smallint', null);
        $this->hasColumn('maps_info', 'string', 300);
        $this->hasColumn('v4_id', 'smallint', 5);
    }

    public function setUp()
    {
        $this->ownsOne('DocumentVersion as document_version', array('local' => 'document_archive_id', 'foreign' => 'document_archive_id'));
    }
}
