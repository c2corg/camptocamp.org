<?php
/**
 * $Id: BaseSummitRoute.class.php 2263 2007-11-03 17:10:43Z fvanderbiest $
 */
class BaseSummitRoute extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_summits_routes');

        $this->hasColumn('summit_id', 'integer', 10, array('primary' => true));
        $this->hasColumn('route_id', 'integer', 10, array('primary' => true));
    }
    
    public function setUp()
    {
        $this->hasOne('Summit', 'SummitRoute.summit_id');
        $this->hasOne('Route', 'SummitRoute.route_id');
    }
}
