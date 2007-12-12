<?php
/**
 * $Id: BaseAreaI18nArchive.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseAreaI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_areas_i18n_archives');

        $this->hasColumn('area_i18n_archive_id', 'integer', 11);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'AreaI18nArchive.user_id_i18n');
    }
}
