<?php
/**
 * $Id: BaseUserI18n.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseUserI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('users_i18n');
    }

    public function setUp()
    {
        $this->ownsOne('User as user', array('local' => 'id', 'foreign' => 'id'));
    }
}
