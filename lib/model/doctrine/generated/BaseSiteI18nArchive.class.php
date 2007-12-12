<?php
/**
 * $Id: BaseSiteI18nArchive.class.php 1934 2007-09-30 15:17:49Z alex $
 */

class BaseSiteI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_sites_i18n_archives');

        $this->hasColumn('site_i18n_archive_id', 'integer', 11);
        $this->hasColumn('remarks', 'string', null);
        $this->hasColumn('pedestrian_access', 'string', null);
        $this->hasColumn('way_back', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'SiteI18nArchive.user_id_i18n');
    }
}
