<?php
/**
 * $Id: BaseRouteI18n.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseRouteI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('routes_i18n');

        $this->hasColumn('remarks', 'string', null);
        $this->hasColumn('gear', 'string', null);
        $this->hasColumn('external_resources', 'string', null);
        $this->hasColumn('route_history', 'string', null);
        $this->hasColumn('v4_id', 'smallint', 5);
        $this->hasColumn('v4_app', 'string', 3);
    }

    public function setUp()
    {
        $this->ownsOne('Route as Route', 'RouteI18n.id');
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
    }
}
