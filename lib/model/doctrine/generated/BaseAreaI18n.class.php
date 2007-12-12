<?php
/**
 * $Id: BaseAreaI18n.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseAreaI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('areas_i18n');
    }

    public function setUp()
    {
        $this->ownsOne('Area as Area', 'AreaI18n.id');
    }
}
