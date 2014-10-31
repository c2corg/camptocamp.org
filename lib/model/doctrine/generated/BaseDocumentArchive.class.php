<?php
/**
 * $Id: BaseDocumentArchive.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseDocumentArchive extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_documents_archives');

        $this->hasColumn('id', 'integer', 10);
        $this->hasColumn('is_protected', 'boolean', null, array('default' => false));
        $this->hasColumn('is_latest_version', 'boolean');
        $this->hasColumn('redirects_to', 'integer', 10);
        $this->hasColumn('lon', 'double', null);
        $this->hasColumn('lat', 'double', null);
        $this->hasColumn('elevation', 'smallint', 4);
        $this->hasColumn('module', 'string', 20);
        $this->hasColumn('document_archive_id', 'integer', 11, array ('primary'));
        $this->hasColumn('geom_wkt', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('Document as document', 'Document.id');
        $this->ownsOne('DocumentVersion as version', array('local' => 'document_archive_id', 'foreign' => 'document_archive_id'));
    }
}
