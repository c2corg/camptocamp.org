<?php
/**
 * $Id: BaseHutI18nArchive.class.php 2045 2007-10-11 18:40:19Z alex $
 */

class BaseHutI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_huts_i18n_archives');

        $this->hasColumn('hut_i18n_archive_id', 'integer', 11);
        $this->hasColumn('staffed_period', 'string', null);
        $this->hasColumn('pedestrian_access', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'HutI18nArchive.user_id_i18n');
    }
}
