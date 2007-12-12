<?php
/**
 * $Id: BasePunbbTopics.class.php 2074 2007-10-17 12:49:54Z alex $
 */

class BasePunbbTopics extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('punbb_topics');
        $this->hasColumn('id', 'integer', 10,  array('primary', 'seq' => 'punbb_topics_id'));
        $this->hasColumn('poster', 'string', 200);
        $this->hasColumn('subject', 'string', 255);
        $this->hasColumn('num_replies', 'integer', 10);
        $this->hasColumn('forum_id', 'integer', 10);
        $this->hasColumn('last_post', 'integer', 10);
    }

    public function setUp()
    {
        $this->hasOne('PunbbForums as Forum', array('local' => 'forum_id', 'foreign' => 'id'));
        //$this->hasMany('BasePunbbComm as Comment', array('local' => 'id', 'foreign' => 'topic_id'));
    }
}
