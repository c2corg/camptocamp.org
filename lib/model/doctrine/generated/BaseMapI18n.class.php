<?php
/**
 * $Id: BaseMapI18n.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseMapI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('maps_i18n');
    }

    public function setUp()
    {
        $this->ownsOne('Map as Map', 'MapI18n.id');
    }
}
