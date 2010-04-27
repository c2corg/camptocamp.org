<?php

class BaseProductI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_products_i18n_archives');

        $this->hasColumn('product_i18n_archive_id', 'integer', 11);
        $this->hasColumn('hours', 'string', null);
        $this->hasColumn('access', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'ProductI18nArchive.user_id_i18n');
    }
}
