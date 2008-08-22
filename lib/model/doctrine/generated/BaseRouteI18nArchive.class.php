<?php
/**
 * $Id: BaseRouteI18nArchive.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseRouteI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_routes_i18n_archives');

        $this->hasColumn('route_i18n_archive_id', 'integer', 11);
        $this->hasColumn('remarks', 'string', null);
        $this->hasColumn('gear', 'string', null);
        $this->hasColumn('external_resources', 'string', null);
        $this->hasColumn('route_history', 'string', null);
        $this->hasColumn('v4_id', 'smallint', 5); 
        $this->hasColumn('v4_app', 'string', 3);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'RouteI18nArchive.user_id_i18n');
    }
}
