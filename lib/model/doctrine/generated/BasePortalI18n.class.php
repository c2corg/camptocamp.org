<?php

class BasePortalI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('portals_i18n');

        $this->hasColumn('abstract', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Portal as Portal', 'PortalI18n.id');
    }
}
