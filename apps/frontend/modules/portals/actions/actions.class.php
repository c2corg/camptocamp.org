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
            $has_map = !empty($has_map);
            $this->has_map = $has_map;
            if ($has_map)
            {
                $map_filter_temp = $this->document->get('map_filter');
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
                if (empty($map_filter['extent']))
                {
                    $map_filter['extent'] = null;
                }
                if (empty($map_filter['height']))
                {
                    $map_filter['height'] = null;
                }
                $this->map_filter = $map_filter;
            }
            
            // user filters:
            $perso = c2cPersonalization::getInstance();
            $langs = $ranges = $activities = array();
            if ($perso->isMainFilterSwitchOn())
            {
                $langs = $perso->getLanguagesFilter();
                $names = array_keys($main_params);
                if (!array_intersect($names, array('areas', 'summits', 'sites', 'huts', 'parkings', 'routes')))
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
                $latest_outings = Outing::listLatest($nb_outings, $langs, $ranges, $activities, $outing_params);
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
            $nb_images = $this->document->get('nb_images');
            $has_images = !empty($nb_images);
            $this->has_images = $has_images;
            if ($has_images)
            {
                $image_url_params = array();
                $image_params = $this->document->get('image_filter');
                $image_params = unpackUrlParameters($image_params, $image_url_params);
                $latest_images = Image::listLatest($nb_images, $langs, $activities, $image_params);
                $this->latest_images = Language::getTheBest($latest_images, 'Image');
                $this->image_url_params = $image_url_params;
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
                $this->latest_threads = PunbbTopics::listLatest($nb_topics,
                                                                $langs, $activities,
                                                                $forum_filter);
            }

            // forum 'mountain news' latest active threads
            $nb_news = $this->document->get('nb_news');
            $has_news = !empty($nb_news);
            $this->has_news = $has_news;
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
                $this->latest_mountain_news = PunbbTopics::listLatestMountainNews($nb_news,
                                                                                  $langs, $activities,
                                                                                  $news_filter);
            }
            
            if ($id == sfConfig::get('app_changerdapproche_id'))
            {
                $description = $this->document->get('description');
                $has_description = !empty($description);
                $this->has_description = $has_description;
                
                $this->setTemplate('changerdapproche');
            }
        }
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'wnam': return 'mi.search_name';
            case 'walt': return 'm.elevation';
            case 'act':  return 'm.activities';
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        $this->buildCondition($conditions, $values, 'Config', '', 'all', 'all');
        if (isset($conditions['all']) && $conditions['all'])
        {
            return array($conditions, $values);
        }
        
        // area criteria
        if ($areas = $this->getRequestParameter('areas'))
        {
            $this->buildCondition($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area');
        }
        elseif ($bbox = $this->getRequestParameter('bbox'))
        {
            Document::buildBboxCondition($conditions, $values, 'm.geom', $bbox);
        }
        
        // portal criteria

        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('wnam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'walt');
        $this->buildCondition($conditions, $values, 'Array', 'activities', 'act');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');
        $this->buildCondition($conditions, $values, 'List', 'm.id', 'id');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
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

        return $out;
    }
}
