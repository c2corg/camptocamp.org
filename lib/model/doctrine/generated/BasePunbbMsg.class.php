<?php
/**
 * $Id: BasePunbbMsg.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BasePunbbMsg extends sfDoctrineRecord
{

    public function setTableDefinition()
    {
        $this->setTableName('punbb_messages');
        $this->hasColumn('id', 'integer', 10,  array('primary', 'seq' => 'punbb_messages_id'));
        $this->hasColumn('owner', 'interger', 10);
        $this->hasColumn('showed', 'smallint', 2);
    }

    public function setUp()
    {
    }
}
