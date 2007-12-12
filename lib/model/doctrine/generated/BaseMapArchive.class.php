<?php
/**
 * $Id: BaseMapArchive.class.php 2420 2007-11-26 12:13:54Z fvanderbiest $
 */

class BaseMapArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_maps_archives');

        $this->hasColumn('map_archive_id', 'integer', 11);
        $this->hasColumn('editor', 'string', 20);
        $this->hasColumn('scale', 'integer', null);
        $this->hasColumn('code', 'string', 20);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
