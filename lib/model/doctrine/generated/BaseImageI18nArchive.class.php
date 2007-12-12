<?php
/**
 * $Id: BaseImageI18nArchive.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseImageI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_images_i18n_archives');

        $this->hasColumn('image_i18n_archive_id', 'integer', 11);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'ImageI18nArchive.user_id_i18n');
    }
}
