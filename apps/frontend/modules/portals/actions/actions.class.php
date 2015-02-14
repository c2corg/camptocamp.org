<?php
/**
 * portals module actions.
 *
 * @package    c2corg
 * @subpackage portals
 */
class portalsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Portal';

    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        
        $mobile_version = c2cTools::mobileVersion();

        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            sfLoader::loadHelpers(array('Pagination'));

            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();
            $document = $this->document;
            $id = $this->document->get('id');
            $topo_filter = $this->document->get('topo_filter');
            $url_params = array();
            $main_params = unpackUrlParameters($topo_filter, $main_url_params);
            
            // map filter
            $has_map = $this->document->get('has_map');
            $has_map = (!$mobile_version && !empty($has_map));
            $this->has_map = $has_map;
            if ($has_map)
            {
                $map_filter_temp = $this->document->get('map_filter');
                $map_filter_temp = str_replace(' ', '', $map_filter_temp);
                $map_filter_temp = explode('|', $map_filter_temp);
                $map_filter = array();
                foreach ($map_filter_temp as $filter)
                {
                    $filter = explode(':', $filter);
                    if (isset($filter[1]))
                    {
                        $map_filter[$filter[0]] = $filter[1];
                    }
                }
                if (empty($map_filter['objects']))
                {
                    $map_filter['objects'] = null;
                }
                if (!empty($map_filter['lon']) || !empty($map_filter['lat']) || !empty($map_filter['zoom']))
                {
                    if (empty($map_filter['lon']))
                    {
                        $lon = $this->document->get('lon');
                        if (is_null($lon))
                        {
                            $lon = 7;
                        }
                    }
                    else
                    {
                        $lon = $map_filter['lon'];
                    }
                    
                    if (empty($map_filter['lat']))
                    {
                        $lat = $this->document->get('lat');
                        if (is_null($lat))
                        {
                            $lat = 45.5;
                        }
                    }
                    else
                    {
                        $lat = $map_filter['lat'];
                    }
                    
                    if (empty($map_filter['zoom']))
                    {
                        $zoom = 6;
                    }
                    else
                    {
                        $zoom = $map_filter['zoom'];
                    }
                    
                    $map_filter['center'] = array($lon, $lat, $zoom);
                }
                else
                {
                    $map_filter['center'] = null;
                }
                if (empty($map_filter['height']))
                {
                    $map_filter['height'] = null;
                }
                $this->map_filter = $map_filter;
                $this->has_geom = false;
            }
            
            // user filters:
            $perso = c2cPersonalization::getInstance();
            $langs = $ranges = $activities = array();
            if ($perso->isMainFilterSwitchOn())
            {
                $langs = $perso->getLanguagesFilter();
                $names = array_keys($main_params);
                if (!array_intersect($names, array('areas', 'summits', 'sites', 'huts', 'parkings', 'routes', 'books')))
                {
                    $ranges = $perso->getPlacesFilter();
                }
                if (!in_array('act', $names))
                {
                    $activities = $perso->getActivitiesFilter();
                }
            }

            // latest outings
            $nb_outings = $this->document->get('nb_outings');
            $has_outings = !empty($nb_outings);
            $this->has_outings = $has_outings;
            if ($has_outings)
            {
                $outing_url_params = array();
                $outing_params = $this->document->get('outing_filter');
                $outing_params = unpackUrlParameters($outing_params, $outing_url_params);
                $outing_langs = $langs;
                $outing_ranges = $ranges;
                $outing_activities = $activities;
                if (isset($outing_params['perso']))
                {
                    $perso_params = explode('-', $outing_params['perso']);
                    if (array_intersect(array('areas', 'act', 'cult', 'no'), $perso_params))
                    {
                        if (!in_array('cult', $perso_params))
                        {
                            $outing_langs = array();
                        }
                        if (!in_array('areas', $perso_params))
                        {
                            $outing_ranges = array();
                        }
                        if (!in_array('act', $perso_params))
                        {
                            $outing_activities = array();
                        }
                    }
                }
                $latest_outings = Outing::listLatest($nb_outings, $outing_langs, $outing_ranges, $outing_activities, $outing_params);
                // choose best language for outings and regions names
                $latest_outings = Language::getTheBest($latest_outings, 'Outing');
                $this->latest_outings = Language::getTheBestForAssociatedAreas($latest_outings);
                $this->outing_url_params = $outing_url_params;
            }

            // latest articles
            $nb_articles = $this->document->get('nb_articles');
            $has_articles = !empty($nb_articles);
            $this->has_articles = $has_articles;
            if ($has_articles)
            {
                $article_url_params = array();
                $article_params = $this->document->get('article_filter');
                $article_params = unpackUrlParameters($article_params, $article_url_params);
                $this->latest_articles = Article::listLatest($nb_articles, $langs, $activities, $article_params);
                $this->article_url_params = $article_url_params;
            }
            
            // latest images
            if ($mobile_version)
            {
                $nb_images = sfConfig::get('app_recent_documents_images_mobile_limit');
            }
            else
            {
                $nb_images = $this->document->get('nb_images');
            }
            $has_images = !empty($nb_images);
            $this->has_images = $has_images;
            if ($has_images)
            {
                $image_url_params = array();
                $image_params = $this->document->get('image_filter');
                $image_params = unpackUrlParameters($image_params, $image_url_params);
                $latest_images = Image::listLatest($nb_images, $langs, $ranges, $activities, $image_params);
                $this->latest_images = Language::getTheBest($latest_images, 'Image');
                $this->image_url_params = $image_url_params;
            }
            
            // latest videos
            $nb_videos = $this->document->get('nb_videos');
            $has_videos = (!$mobile_version && !empty($nb_videos));
            if ($has_videos)
            {
                $video_url_params = array();
                $video_params = $this->document->get('video_filter');
                $video_params = explode('|', $video_params, 3);
                if (count($video_params) == 3)
                {
                    $video_item = array('url' => trim($video_params[0]),
                                           'thumbnail' => trim($video_params[1]),
                                           'title' => trim($video_params[2]));
                    $latest_videos = array($video_item);
                    $this->latest_videos = $latest_videos;
                }
                else
                {
                    $has_videos = false;
                }
            }
            $this->has_videos = $has_videos;
            
            // forum 'mountain news' latest active threads
            $nb_news = $this->document->get('nb_news');
            $has_news = !empty($nb_news);
            $this->has_news = $has_news;
            $news_filter_ids = array();
            if ($has_news)
            {
                $news_filter_temp = $this->document->get('news_filter');
                $news_filter_temp = explode('|', $news_filter_temp);
                $news_filter = array();
                foreach ($news_filter_temp as $filter)
                {
                    $filter = explode(':', $filter);
                    if (isset($filter[1]))
                    {
                        $news_filter[$filter[0]] = explode(',', $filter[1]);
                    }
                }
                $news_filter_ids = PunbbTopics::getForumIds('app_forum_mountain_news', $langs, $activities, $news_filter);
                $this->latest_mountain_news = PunbbTopics::listLatestById($nb_news, $news_filter_ids);
                $this->news_filter_ids = implode('-', $news_filter_ids);
            }

            // forum latest active threads
            $nb_topics = $this->document->get('nb_topics');
            $has_topics = !empty($nb_topics);
            $this->has_topics = $has_topics;
            if ($has_topics)
            {
                $forum_filter_temp = $this->document->get('forum_filter');
                $forum_filter_temp = explode('|', $forum_filter_temp);
                $forum_filter = array();
                foreach ($forum_filter_temp as $filter)
                {
                    $filter = explode(':', $filter);
                    if (isset($filter[1]))
                    {
                        $forum_filter[$filter[0]] = explode(',', $filter[1]);
                    }
                }
                $forum_filter_ids = PunbbTopics::getForumIds('app_forum_public_ids', $langs, $activities, $forum_filter);
                $this->latest_threads = PunbbTopics::listLatestById($nb_topics, $forum_filter_ids);
                $this->forum_filter_ids = implode('-', array_merge($news_filter_ids, $forum_filter_ids));
            }

            $cda_config = sfConfig::get('app_portals_cda');
            if ($id == $cda_config['id'])
            {
                $description = $this->document->get('description');
                $has_description = !empty($description);
                $this->has_description = $has_description;
                
                $this->setTemplate('changerdapproche');
            }
            
            sfLoader::loadHelpers(array('sfBBCode', 'SmartFormat'));
            $abstract = strip_tags(parse_links(parse_bbcode_abstract($this->document->get('abstract'))));
            $this->getResponse()->addMeta('description', $abstract);
        }
    }

    /**
     * Executes edit action.
     */
    public function executeEdit()
    {
        $id = $this->getRequestParameter('id');
        $user = $this->getUser();
        $is_moderator = $user->hasCredential(sfConfig::get('app_credentials_moderator'));
    
    //    FIXME : only moderators can edit a portal, waiting for correct edition right management :
    //      - for common portals, the members con edit only text fields, and data fields are editable only by moderators
    //      - for cda portal, the moderators only can edit it
    //    $cda_config = sfConfig::get('app_portals_cda');
    //    if ($is_moderator || $id != $cda_config['id'])
        if ($is_moderator)
        {
            parent::executeEdit();
        }
        else
        {
            return $this->ajax_feedback('You do not have enough credentials to perform this operation');
        }
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant parameters.
     * @return array
     */
    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        
        $this->addNameParam($out, 'wnam');
        $this->addCompareParam($out, 'walt');
        $this->addListParam($out, 'act');
        $this->addParam($out, 'geom');
        $this->addParam($out, 'wcult');

        return $out;
    }
}
