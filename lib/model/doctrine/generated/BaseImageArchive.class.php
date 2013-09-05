<?php
/**
 * $Id: BaseImageArchive.class.php 2312 2007-11-07 00:27:05Z alex $
 */

class BaseImageArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_images_archives');

        $this->hasColumn('image_archive_id', 'integer', 11);
        $this->hasColumn('filename', 'string', null);
        $this->hasColumn('has_svg', 'boolean', null);
        $this->hasColumn('width', 'smallint', null);
        $this->hasColumn('height', 'smallint', null);
        $this->hasColumn('file_size', 'integer', null);
        $this->hasColumn('categories', 'string', null); // array
        $this->hasColumn('camera_name', 'string', 100);
        $this->hasColumn('exposure_time', 'double', null);
        $this->hasColumn('fnumber', 'double', null);
        $this->hasColumn('focal_length', 'double', null);
        $this->hasColumn('iso_speed', 'smallint', 4); 
        $this->hasColumn('date_time', 'timestamp', null);
        $this->hasColumn('image_type', 'smallint');
        $this->hasColumn('v4_id', 'smallint', 5);
        $this->hasColumn('v4_app', 'string', 3);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('author', 'string', 100);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
