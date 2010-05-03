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
            $topo_filter = $document->getRaw('topo_filter');
            $url_params = array();
            $main_params = unpackUrlParameters($topo_filter, $main_url_params);
            
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
            $nb_outings = $document->getRaw('nb_outings');
            if (!empty($nb_outings))
            {
                $outing_url_params = array();
                $outing_params = $document->getRaw('outing_filter');
                $outing_params = unpackUrlParameters($outing_params, $outing_url_params);
                $latest_outings = Outing::listLatest($nb_outings, $langs, $ranges, $activities, $outing_params);
                // choose best language for outings and regions names
                $latest_outings = Language::getTheBest($latest_outings, 'Outing');
                $this->latest_outings = Language::getTheBestForAssociatedAreas($latest_outings);
            }

            $this->latest_articles = Article::listLatest(sfConfig::get('app_recent_documents_articles_limit'),
                                                         $langs, $activities);
            
            $latest_images = Image::listLatest(sfConfig::get('app_recent_documents_images_limit'),
                                                     $langs, $activities);
            $this->latest_images = Language::getTheBest($latest_images, 'Image');
            
            // forum latest active threads
            $nb_topics = $document->getRaw('nb_topics');
            if (!empty($nb_topics))
            {
                $forum_filter = $document->getRaw('forum_filter');
                $this->latest_threads = PunbbTopics::listLatest($nb_topics,
                                                                $langs, $activities,
                                                                $forum_filter);
            }

            // forum 'mountain news' latest active threads
            $nb_news = $document->getRaw('nb_news');
            if (!empty($nb_news))
            {
                $news_filter = $document->getRaw('news_filter');
                $this->latest_mountain_news = PunbbTopics::listLatestMountainNews($nb_news,
                                                                                  $langs, $activities,
                                                                                  $news_filter);
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
