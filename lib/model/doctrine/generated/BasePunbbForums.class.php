<?php
/**
 * $Id: BasePunbbForums.class.php 2074 2007-10-17 12:49:54Z alex $
 */

class BasePunbbForums extends sfDoctrineRecord
{
    public function setTableDefinition()
    {   
        $this->setTableName('punbb_forums');
        $this->hasColumn('id', 'integer', 2,  array('primary'));
        $this->hasColumn('forum_name', 'string', 80);
        $this->hasColumn('culture', 'string', 2);
    }

    public function setUp()
    {
        $this->ownsMany('PunbbTopics as PunbbTopics', array('foreign' => 'forum_id', 'local' => 'id'));
    }
}
