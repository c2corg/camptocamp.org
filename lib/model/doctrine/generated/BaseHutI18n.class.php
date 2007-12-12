<?php
/**
 * $Id: BaseHutI18n.class.php 2045 2007-10-11 18:40:19Z alex $
 */

class BaseHutI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('huts_i18n');

        $this->hasColumn('staffed_period', 'string', null);
        $this->hasColumn('pedestrian_access', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Hut as Hut', 'HutI18n.id');
    }
}
