<?php
/**
 * $Id: BaseHutArchive.class.php 1354 2007-08-21 13:11:21Z alex $
 */

class BaseHutArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
 
        $this->setTableName('app_huts_archives');

        $this->hasColumn('hut_archive_id', 'integer', 11);
        $this->hasColumn('shelter_type', 'integer', 1);
        $this->hasColumn('is_staffed', 'boolean', null);
        $this->hasColumn('phone', 'string', 50);
        $this->hasColumn('url', 'string', 255);
        $this->hasColumn('staffed_capacity', 'smallint', 3);
        $this->hasColumn('unstaffed_capacity', 'smallint', 2);
        $this->hasColumn('has_unstaffed_matress', 'samllint', 1);
        $this->hasColumn('has_unstaffed_blanket', 'samllint', 1);
        $this->hasColumn('has_unstaffed_gas', 'samllint', 1);
        $this->hasColumn('has_unstaffed_wood', 'samllint', 1);
        $this->hasColumn('activities', 'string', null); // array
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
 
