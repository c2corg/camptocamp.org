<?php
/**
 * Model for summits
 * $Id: BaseRememberKey.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class BaseRememberKey extends sfDoctrineRecord
{
  
    public function setTableDefinition()
    {
        $this->setTableName('app_remember_keys');

        //$this->hasColumn('id', 'integer', 4, array ('primary|autoincrement'));
        $this->hasColumn('user_id', 'integer', 4, array ('primary'));
        $this->hasColumn('remember_key', 'string', 32, array ());
        $this->hasColumn('ip_address', 'string', 15, array ('primary'));
        $this->hasColumn('created_at', 'timestamp', null, array ());
    }
  
    public function setUp()
    {
        $this->hasOne('User as user', 'RememberKey.user_id');
    }
  
}
