<?php

class BaseProduct extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('products');

        $this->hasColumn('product_type', 'string', null); // array
        $this->hasColumn('url', 'string', 255);
    }

    public function setUp()
    {
        $this->hasMany('ProductI18n as ProductI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('ProductI18n', 'culture');
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
