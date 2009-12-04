<?php
/**
 * $Id: BaseBook.class.php 2261 2007-11-03 15:05:40Z alex $
 */

class BaseBook extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('books');

        $this->hasColumn('author', 'string', 100);
        $this->hasColumn('editor', 'string', 100);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('url', 'string', 255);
        $this->hasColumn('isbn', 'string', 17);
        $this->hasColumn('langs', 'string', null); // array
        $this->hasColumn('book_types', 'string', null); // array
    }

    public function setUp()
    {
        $this->hasMany('BookI18n as BookI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('BookI18n', 'culture');
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id')); 
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
