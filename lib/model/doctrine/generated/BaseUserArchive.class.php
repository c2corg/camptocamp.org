<?php
/**
 * $Id: BaseUserArchive.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseUserArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_users_archives');

        $this->hasColumn('user_archive_id', 'integer', 11);
        $this->hasColumn('v4_id', 'smallint', 5);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('category', 'smallint', 1);
    }

    public function setUp()
    {
        $this->ownsOne('DocumentVersion as document_version', array('local' => 'document_archive_id', 'foreign' => 'document_archive_id'));
    }
}
