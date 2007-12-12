<?php
/**
 * $Id: BaseBookI18n.class.php 1317 2007-08-16 22:20:40Z alex $
 */

class BaseBookI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('books_i18n');
    }

    public function setUp()
    {
        $this->ownsOne('Book as Book', 'BookI18n.id');
    }
}
