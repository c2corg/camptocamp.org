<?php
/**
 * $Id: BaseUserPrivateData.class.php 2156 2007-10-23 18:34:33Z alex $
 */

class BaseUserPrivateData extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_users_private_data');

        $this->hasColumn('id', 'integer', 10, array('primary'));
        $this->hasColumn('username', 'string', 200); // this is the name for the forum
        $this->hasColumn('login_name', 'string', 200, array('unique')); // this is the login
        $this->hasColumn('topo_name', 'string', 200); // this is the name for guidebook
        $this->hasColumn('password', 'string', 255);
        $this->hasColumn('password_tmp', 'string', 255);
        $this->hasColumn('email', 'string', 100);
        $this->hasColumn('document_culture', 'string', 20);
        $this->hasColumn('v4_id', 'smallint', 5);
        $this->hasColumn('registered', 'smallint', null);
        $this->hasColumn('is_profile_public', 'boolean', null, array('default' => false));
        $this->hasColumn('pref_cookies', 'string');
        $this->hasColumn('group_id', 'integer'); // forum group (aka admin, moderator etc)
        
        // forum informations
        $this->hasColumn('language', 'string', 25, array('default' => 'English'));
    }

    public function setUp()
    {
        $this->ownsOne('User as user', 'User.id');
    }
}
