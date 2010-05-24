<?php
/**
 * $Id: BaseBookArchive.class.php 2261 2007-11-03 15:05:40Z alex $
 */

class BaseBookArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_books_archives');

        $this->hasColumn('book_archive_id', 'integer', 11);
        $this->hasColumn('author', 'string', 100);
        $this->hasColumn('editor', 'string', 100);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('url', 'string', 255);
        $this->hasColumn('isbn', 'string', 17);
        $this->hasColumn('langs', 'string', null); // array
        $this->hasColumn('book_types', 'string', null); // array
        $this->hasColumn('publication_date', 'string', 100);
        $this->hasColumn('nb_pages', 'small_int', null);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
