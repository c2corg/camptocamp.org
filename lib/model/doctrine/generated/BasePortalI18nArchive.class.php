<?php

class BasePortalI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_portals_i18n_archives');

        $this->hasColumn('portal_i18n_archive_id', 'integer', 11);
        $this->hasColumn('abstract', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'PortalI18nArchive.user_id_i18n');
    }
}
