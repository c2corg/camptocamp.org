<?php
/**
 * $Id: BaseDocumentI18nArchive.class.php 2247 2007-11-02 13:56:21Z alex $
 */

class BaseDocumentI18nArchive extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_documents_i18n_archives');

        $this->hasColumn('document_i18n_archive_id', 'integer', 11, array('primary' => true));
        $this->hasColumn('id', 'integer', 10);
        $this->hasColumn('culture', 'string', 2);
        $this->hasColumn('name', 'string', 150);
        $this->hasColumn('search_name', 'string', 150);
        $this->hasColumn('description', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as versionI18n', array('local' => 'document_i18n_archive_id', 'foreign' => 'document_i18n_archive_id'));
    }
}
