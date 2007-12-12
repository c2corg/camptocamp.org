<?php
/**
 * $Id: BaseImageI18n.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseImageI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('images_i18n');
    }

    public function setUp()
    {
        $this->ownsOne('Image as Image', 'ImageI18n.id');
    }
}
