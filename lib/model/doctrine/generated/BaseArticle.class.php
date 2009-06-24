<?php
/**
 * $Id: BaseArticle.class.php 2138 2007-10-22 12:03:24Z fvanderbiest $
 */

class BaseArticle extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('articles');

        $this->hasColumn('categories', 'string', null); // array
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('article_type', 'integer', 1); 
    }

    public function setUp()
    {
        $this->hasMany('ArticleI18n as ArticleI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('ArticleI18n', 'culture');
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
    }
}
