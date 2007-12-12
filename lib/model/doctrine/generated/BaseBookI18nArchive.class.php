<?php
/**
 * $Id: BaseBookI18nArchive.class.php 2261 2007-11-03 15:05:40Z alex $
 */

class BaseBookI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_books_i18n_archives');

        $this->hasColumn('book_i18n_archive_id', 'integer', 11);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'BookI18nArchive.user_id_i18n');
    }
}
