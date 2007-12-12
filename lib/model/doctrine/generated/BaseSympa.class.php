<?php
/**
 * $Id: BaseSympa.class.php 1685 2007-09-19 10:13:57Z alex $
 */
class BaseSympa extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('subscriber_table');

        $this->hasColumn('list_subscriber', 'string', 50, array('primary' => true));
        $this->hasColumn('user_subscriber', 'string', 100, array('primary' => true));
    }

    public function setUp()
    {
    }
}
