<?php

class BasePortalArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_portals_archives');

        $this->hasColumn('portal_archive_id', 'integer', 11);
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
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
