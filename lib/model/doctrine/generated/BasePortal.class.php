<?php

class BasePortal extends BaseDocument
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('portals');

        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('has_map', 'boolean', null);
        $this->hasColumn('map_filter', 'string', 255);
        $this->hasColumn('topo_filter', 'string', 255);
        $this->hasColumn('nb_outings', 'small_int', null);
        $this->hasColumn('outing_filter', 'string', 255);
        $this->hasColumn('nb_images', 'small_int', null);
        $this->hasColumn('image_filter', 'string', 255);
        $this->hasColumn('nb_videos', 'small_int', null);
        $this->hasColumn('video_filter', 'string', 255);
        $this->hasColumn('nb_articles', 'small_int', null);
        $this->hasColumn('article_filter', 'string', 255);
        $this->hasColumn('nb_topics', 'small_int', null);
        $this->hasColumn('forum_filter', 'string', 255);
        $this->hasColumn('nb_news', 'small_int', null);
        $this->hasColumn('news_filter', 'string', 255);
        $this->hasColumn('design_file', 'string', 255);
    }

    public function setUp()
    {
        $this->hasMany('PortalI18n as PortalI18n', array('local' => 'id', 'foreign' => 'id'));
        $this->hasI18nTable('PortalI18n', 'culture');
        $this->hasMany('Association as associations', array('local' => 'id', 'foreign' => 'linked_id'));
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }
}
