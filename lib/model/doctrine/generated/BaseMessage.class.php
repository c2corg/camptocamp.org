<?php
/**
 * $Id$
 */

class BaseMessage extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_messages');
        $this->hasColumn('culture', 'string', 2, array('primary'));
        $this->hasColumn('message', 'string', 400); 
    }

    public function setUp()
    {
    }
}