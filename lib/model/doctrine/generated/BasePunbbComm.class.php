<?php
/**
 * $Id: BasePunbbComm.class.php 1019 2007-07-23 18:36:35Z alex $
 */

class BasePunbbComm extends sfDoctrineRecord 
{

    public function setTableDefinition()
    {
        $this->setTableName('punbb_posts');
        $this->hasColumn('id', 'integer', 10,  array('primary', 'seq' => 'punbb_posts_id'));
        $this->hasColumn('poster', 'character', 200);
        $this->hasColumn('poster_id', 'interger', 10);
        $this->hasColumn('message', 'text');
        $this->hasColumn('posted', 'interger', 10);
        $this->hasColumn('topic_id', 'integer', 10);
    }

    public function setUp()
    {
        $this->hasOne('BasePunbbTopics as Topic', array('local' => 'topic_id', 'foreign' => 'id'));
        //$this->hasMany('BasePunbbTopic as Lien', array('local' => 'id', 'foreign' => 'topic_id'));
    }
}
