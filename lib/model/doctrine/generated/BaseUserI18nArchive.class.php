<?php
/**
 * $Id: BaseUserI18nArchive.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseUserI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_users_i18n_archives');

        $this->hasColumn('user_i18n_archive_id', 'integer', 11);
    }

    public function setUp()
    {
    }
}
