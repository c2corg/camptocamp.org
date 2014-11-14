<?php
/**
 * $Id: BaseOutingI18nArchive.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseOutingI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_outings_i18n_archives');

        $this->hasColumn('outing_i18n_archive_id', 'integer', 11);
        $this->hasColumn('participants', 'string', 200);
        $this->hasColumn('timing', 'string', 200);
        $this->hasColumn('weather', 'string', null);
        $this->hasColumn('hut_comments', 'string', null);
        $this->hasColumn('access_comments', 'string', null);
        $this->hasColumn('conditions', 'string', null);
        $this->hasColumn('conditions_levels', 'string', null);
        $this->hasColumn('avalanche_desc', 'string', null);
        $this->hasColumn('outing_route_desc', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'OutingI18nArchive.user_id_i18n');
    }
}
