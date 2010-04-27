<?php

class BaseProductArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_products_archives');

        $this->hasColumn('product_type', 'string', null); // array
        $this->hasColumn('url', 'string', 255);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
