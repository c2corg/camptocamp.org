<?php
/**
 * $Id: BaseSummitRoute.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class BaseRouteOuting extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_routes_outings');

        $this->hasColumn('route_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('outing_id', 'integer', 10, array('primary' => true));
    }
    
    public function setUp()
    {
        $this->hasOne('Route', 'RouteOuting.route_id');
        $this->hasOne('Outing', 'RouteOuting.outing_id');
    }
}
