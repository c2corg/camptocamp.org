<?php

class BaseProductI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('products_i18n');

        $this->hasColumn('hours', 'string', null);
        $this->hasColumn('access', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Product as Product', 'ProductI18n.id');
    }
}
