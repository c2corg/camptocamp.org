<?php
/**
 * $Id: BaseSummitI18nArchive.class.php 1164 2007-08-02 19:51:13Z alex $
 */

class BaseSummitI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_summits_i18n_archives');

        $this->hasColumn('summit_i18n_archive_id', 'integer', 11);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'SummitI18nArchive.user_id_i18n');
    }
}
