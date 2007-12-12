<?php
/**
 * $Id: BaseArticleArchive.class.php 1335 2007-08-20 09:55:45Z fvanderbiest $
 */

class BaseArticleArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_articles_archives');

        $this->hasColumn('article_archive_id', 'integer', 11);
        $this->hasColumn('categories', 'string', null); // array
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('article_type', 'integer', 1);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
