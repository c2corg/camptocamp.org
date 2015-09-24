<?php
/**
 * outings module actions.
 *
 * @package    c2corg
 * @subpackage outings
 * @version    $Id: actions.class.php 2542 2007-12-21 19:07:08Z alex $
 */
class outingsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Outing';

    /**
     * Nb of dimensions for geom column
     */   
    protected $geom_dims = 4; 
   
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();

        // redefine page title: prepend date
        sfLoader::loadHelpers(array('Date'));
        $title = $this->document->get('name')
               . ', ' . format_date($this->document->get('date'), 'D')
               . ' :: ' . $this->__('outing');
        $this->setPageTitle($title);
        
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();
            
            $associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
    
            // determines outing max elevation using routes max elevations if it is not set
            $max_elevation = $this->document->getMaxElevation();
            if ($max_elevation == NULL || $max_elevation == 0)
            {
                $outing_max_elevation = 0;
                foreach ($associated_routes as $route)
                {
                    $route_max_elevation = ($route['max_elevation'] instanceof Doctrine_Null) ? 0 : $route['max_elevation'];
                    if ($route_max_elevation > $outing_max_elevation)
                    {
                        $outing_max_elevation = $route_max_elevation;
                    }
                }
    
                if ($outing_max_elevation > 0)
                {
                    $this->document->setMaxElevation($outing_max_elevation);
                }
            }
            
            // the routes/sites linked to the outing will be used to retrieve 2-hops docs like summits,
            // huts, parkings....
            // However, we need some special handling with raid routes (more than 2 days)
            $parent_ids = $other_ids = $other_routes = $default_ids = array();
            $associated_summits = array();
            $associated_huts = array();
            $associated_parkings = array();
            if (count($associated_routes))
            {
                $associated_routes = c2cTools::sortArray($associated_routes, 'duration');
                foreach ($associated_routes as $route)
                {
                    // pour les docs de 2?me niveau, on retient uniquement les itin?raires de 1 ou 2 jours
                    if (!$route['duration'] instanceof Doctrine_Null)
                    {
                        if ($route['duration'] <= 4)
                        {
                            $parent_ids[] = $route['id'];
                        }
                        else
                        {
                            $other_routes[$route['id']] = $route['duration'];
                        }
                    }
                    else
                    {
                        $default_ids[] = $route['id'];
                    }
                }
                // s'il n'y a pas d'itin?raire de 1 ou 2 jours, on utilise les itin?raires qui ont la plus petite dur?e
                // s'il n'y en a pas non plus, on utilise les itin?raire dont la dur?e est non renseign?e
                if (!count($parent_ids))
                {
                    if (count($other_routes) > 1)
                    {
                        asort($other_routes);
                        $min_duration = $other_routes.reset();
                        foreach ($other_routes as $id => $duration)
                        {
                            if ($duration == $min_duration)
                            {
                                $other_ids[] = $id;
                            }
                        }
                    }
                    elseif (count($other_routes))
                    {
                        $other_ids[] = key($other_routes);
                    }
                    else
                    {
                        $other_ids = $default_ids;
                    }
                    $parent_ids = $other_ids;
                }
            }
            if (count($this->associated_sites))
            {
                $associated_sites = $this->associated_sites;
                foreach ($associated_sites as $site)
                {
                    $parent_ids[] = $site['id'];
                }
            }

            // now retrieve the associated docs (summits, huts, parkings)
            if (count($parent_ids))
            {
                $associated_route_docs = Association::findLinkedDocsWithBestName($parent_ids, $prefered_cultures, array('sr', 'hr', 'pr', 'pt'), false, false);
                if (count($associated_route_docs))
                {
                    $associated_route_docs = c2cTools::sortArray($associated_route_docs, 'elevation');
                    $associated_summits = array_filter($associated_route_docs, array('c2cTools', 'is_summit'));
                    $associated_huts = array_filter($associated_route_docs, array('c2cTools', 'is_hut'));
                    $associated_parkings = Parking::getAssociatedParkingsData(array_filter($associated_route_docs, array('c2cTools', 'is_parking')));
                    
                    if (count($associated_summits) && count($associated_huts))
                    {
                        $summit_ids = $summit_hut_tmp = $summit_hut_ids = array();
                        foreach ($associated_summits as $summit)
                        {
                            $summit_ids[] = $summit['id'];
                        }
                        $summit_hut_tmp = Association::countAllLinked($summit_ids, 'sh');
                        if (count($summit_hut_tmp))
                        {
                            foreach ($summit_hut_tmp as $hut)
                            {
                                $summit_hut_ids[] = $hut['main_id'];
                            }
                            foreach ($associated_summits as $key => $summit)
                            {
                                if (in_array($summit['id'], $summit_hut_ids))
                                {
                                    unset($associated_summits[$key]);
                                }
                            }
                        }
                    }
                }
            }
            
            $this->associated_summits = $associated_summits;
            $this->associated_huts = $associated_huts;
            $this->associated_parkings = $associated_parkings;
            $this->associated_routes = $associated_routes;

            // associated users
            $associated_users = array_filter($this->associated_docs, array('c2cTools', 'is_user'));
            if (count($associated_users) >= 2)
            {
                // Set outing creator at first in the users list, and sort other users by name
                $creator_id = $this->document->getCreatorId();
                $creator = array();
                $associated_users_2 = array();
                foreach ($associated_users as $key => $user)
                {
                    if ($user['id'] == $creator_id)
                    {
                        $creator[$key] = $user;
                    }
                    else
                    {
                        $associated_users_2[$key] = $user;
                    }
                }
                if (count($associated_users_2) >= 2)
                {
                    $associated_users_2 = c2cTools::sortArrayByName($associated_users_2);
                }
                $associated_users = array_merge($creator, $associated_users_2);
            }
            $this->associated_users = $associated_users;

            // related portals
            $related_portals = array();
            $activities = $this->document->get('activities');
            $outing_with_public_transportation = $this->document->get('outing_with_public_transportation');
            if (!$outing_with_public_transportation instanceof Doctrine_Null && $outing_with_public_transportation)
            {
                $related_portals[] = 'cda';
            }
            Portal::getRelatedPortals($related_portals, $this->associated_areas, $associated_routes, $activities);
            $this->related_portals = $related_portals;
    
            $description = array($title, $this->getActivitiesList(), $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
        else
        {
            // only moderators and associated users should see archive versions of outings
            $this->filterAuthorizedPeople($this->getRequestParameter('id'));
        }
    }

    public function executeDiff()
    {
        $id = $this->getRequestParameter('id');
        $this->filterAuthorizedPeople($id);
        parent::executeDiff();
    }

    public function executeHistory()
    {
        $id = $this->getRequestParameter('id');
        $this->filterAuthorizedPeople($id);
        parent::executeHistory();
    }

    public function setEditFormInformation()
    {
        parent::setEditFormInformation();
        if (!$this->new_document)
        {
            // retrieve associated articles, for use in the MW contest checkbox
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $id = $this->getRequestParameter('id');
            $this->associated_articles = Association::findAllAssociatedDocs($id, array('id'), 'oc');
        }
    }

    protected function endEdit()
    {
        //Test if form is submitted or not
        if ($this->success) // form submitted and success (doc has been saved)
        {
            // if this is the first version of the outing (aka creation)
            // set a flash message to encourage to also enhance the corresponding route
            if (is_null($this->document->getVersion()))
            {
                $this->setNotice('thanks for new outing');
            }

            // try to perform association with linked_doc (if pertinent)
            $associated_id = $this->getRequestParameter('document_id');
            $user_id = $this->getUser()->getId();
            $id = $this->document->get('id');
        
            if (($this->new_document && $associated_id ) || ($associated_id && !Association::find($associated_id, $id)))  // can be a 'to' or 'ro' Association
            {
                // we must get this document's module (site or route ?)
                $associated_doc = Document::find('Document', $associated_id, array('module'));
                if ($associated_doc) 
                {
                    $associated_module = $associated_doc->get('module');
            
                    $a = new Association();
                    if ($associated_module == 'routes')
                    {
                        $a->doSaveWithValues($associated_id, $id, 'ro', $user_id); // main, linked, type
                        // clear cache of associated route ...
                        $this->clearCache('routes', $associated_id, false, 'view');
                    }
                    elseif ($associated_module == 'sites')
                    {
                        $a->doSaveWithValues($associated_id, $id, 'to', $user_id); // main, linked, type
                        // clear cache of associated site ...
                        $this->clearCache('sites', $associated_id, false, 'view');
                    }
                    
                    // here if we have created a new document and if $this->document->get('geom_wkt') is null, then use associated doc geom associations:
                    // this allows us to filter on ranges even if no GPX is uploaded
                    if ($this->new_document && $associated_id && !$this->document->get('geom_wkt'))
                    {
                        // get all associated regions (only regions, countries, depts, no maps !) with this summit:
                        $associations = GeoAssociation::findAllAssociations($associated_id, array('dr', 'dc', 'dd', 'dv'));
                        // replicate them with outing_id instead of (route_id or site_id):
                        foreach ($associations as $ea)
                        {
                            $areas_id = $ea->get('linked_id');
                            $a = new GeoAssociation();
                            $a->doSaveWithValues($id, $areas_id, $ea->get('type'));
                            // clear cache of associated areas
                            $this->clearCache('areas', $areas_id, false, 'view');
                        }
                    }
                    
                }
            }    

            // create also association with current user.
            if ($this->new_document)
            {
                $uo = new Association();
                $uo->doSaveWithValues($user_id, $id, 'uo', $user_id); // main, linked, type
                // clear cache of current user
                $this->clearCache('users', $user_id, false, 'view');
            }    
            
            // create association with MW contest article, if requested
            if ($this->new_document)
            {
                $mw_contest_associate = $this->getRequestParameter('mw_contest_associate');
                if ($mw_contest_associate)
                {
                    $mw_article_id = sfConfig::get('app_mw_contest_id');
                    $oc = new Association();
                    $oc->doSaveWithValues($id, $mw_article_id, 'oc', $user_id);
                }
            }
            
            parent::endEdit(); // redirect to document view
        }
    }

    /**
     * populates custom fields (for instance if we are creating a new outing, already associated with a route)
     * overrides the one in documentsActions class.
     */
    protected function populateCustomFields()
    {
        $document = $this->document;
 
        if ($this->getRequestParameter('link') && 
            $linked_doc = Document::find('Document', $this->getRequestParameter('link'), array('module')))
        {
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            switch ($linked_doc->get('module'))
            {
                case 'routes':
                    $linked_doc = Document::find('Route', $this->getRequestParameter('link'), array('activities', 'max_elevation', 'min_elevation', 'height_diff_up', 'height_diff_down'));

                    $linked_doc->setBestCulture($prefered_cultures);
                    $this->linked_doc = $linked_doc;
            
                    // FIXME: this "field getting" triggers an additional request to the db (yet already hydrated), 
                    // probably because activities field has been hydrated into object as a string and not as an array
                    // cf filterGetActivities and filterSetActivities in Route.class.php ...
                    $activities = $linked_doc->get('activities');
                    $document->set('activities', $activities);

                    if ($max_elevation = $linked_doc->get('max_elevation'))
                    {
                        $document->set('max_elevation', $max_elevation);
                    }
                    
                    if ($min_elevation = $linked_doc->get('min_elevation'))
                    {
                        $document->set('access_elevation', $min_elevation);
                        if (in_array(1, $activities)) // ski
                        {
                            $document->set('up_snow_elevation', $min_elevation);
                            $document->set('down_snow_elevation', $min_elevation);
                        }
                    }
            
                    if ($height_diff_up = $linked_doc->get('height_diff_up'))
                    {
                        $document->set('height_diff_up', $height_diff_up);
                    }

                    if ($height_diff_down = $linked_doc->get('height_diff_down'))
                    {
                        $document->set('height_diff_down', $height_diff_down);
                    }
            
                    // find associated summits to this route, extract the highest and create document with this name.
                    $associated_summits = array_filter(Association::findAllWithBestName($linked_doc->get('id'), $prefered_cultures), array('c2cTools', 'is_summit'));
 
                    $this->highest_summit_name = c2cTools::extractHighestName($associated_summits);
                    $document->set('name', $this->highest_summit_name . $this->__(' :') . ' ' . $linked_doc->get('name'));
                    
                    break;
            
                case 'sites':
                    $linked_doc = Document::find('Site', $this->getRequestParameter('link'), array('mean_rating'));
                    $linked_doc->setBestCulture($prefered_cultures);
                    $document->set('name', $linked_doc->get('name'));
                    $document->set('activities', array(4));
                    $this->linked_doc = $linked_doc;
                    break;
                default:
                    $this->setErrorAndRedirect('You cannot create an outing without linking it to an existing route or site3', '@default_index?module=outings');
            }   
        }
        $this->document = $document;
    }

    /**
     * Executes Wizard action.
     * Everything is done in view ...
     */
    public function executeWizard()
    {
        
    }
    
    /**
     * filter for people who have the right to edit current document (linked people for outings, original editor for articles ....)
     * overrides the one in parent class.
     */
    protected function filterAuthorizedPeople($id)
    {
        // we know here that document $id exists and that its model is the current one (Outing).
        // we must guess the associated people and restrain edit rights to these people + moderator.

        $user = $this->getUser();
        $a = Association::find($user->getId(), $id, 'uo');
        
        if (!$a && !$user->hasCredential('moderator'))
        {
            $referer = $this->getRequest()->getReferer();
            $this->setErrorAndRedirect('You do not have the rights to edit this outing', $referer);
        }
    }
    
    /**
     * filter edits which must require additional parameters (link for instance : outing with route or with site)
     * overrides the one in parent class.
     */
    protected function filterAdditionalParameters()
    {
        if (!$this->getRequestParameter('document_id') && !$this->getRequestParameter('link'))
        {
            $this->setErrorAndRedirect('You cannot create an outing without linking it to an existing route or site', '@default_index?module=outings');
        }
        
        $id = $this->getRequestParameter('link', 0) + $this->getRequestParameter('document_id', 0);
        
        // linked_doc already retrieved in populateCustomFields() except when creating a new outing
        if (isset($this->linked_doc))
        {
            $linked_doc = $this->linked_doc;
        }
        else
        {
            // route (most of the time) or site
            $linked_doc = Document::find('Route', $id, array('id', 'module'));
            if (!$linked_doc)
            {
                $linked_doc = Document::find('Site', $id, array('id', 'module'));
            }
        }

        if ($linked_doc && $linked_doc->get('module') == 'routes')
        {
            if ($this->document)
            {
	              $linked_doc->set('name', $this->document->get('name')); // contains highest summit too
            }
        }
        
        if (!$linked_doc)
        {
            $this->setErrorAndRedirect('You cannot create an outing without linking it to an existing route or site1', '@default_index?module=outings');
        }
    }
    
    /**
     * This function is used to get a DB query result formatted in HTML. It is used
     * from the generic action class (in the documents module)
     */
    protected function getFormattedResult($result) {

        // Explicitely load helpers (required in the controller)        
        sfLoader::loadHelpers(array('Tag', 'Url', 'Javascript'));
        
        //$list = sfConfig::get('mod_routes_facings_list');
        
        $html  = '<td>' . link_to($result['name'], '@document_by_id?module=outings&id=' . $result['id']) . '</td>';
        $html .= '<td>' . $result['min_elevation'] . '</td>';
        $html .= '<td>' . $result['max_elevation'] . '</td>';

        return $html;
    }

    
    public function executeFilterredirect()
    {
        if ($this->getRequestParameter('cond') || $this->getRequestParameter('format'))
        {
            $action = 'conditions';
        }
        else
        {
            $action = 'list';
        }
        $route = '/' . $this->getModuleName() . '/' . $action; 
        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $criteria = array_merge($this->filterSearchParameters(),
                                    $this->filterSortParameters());
            if ($criteria)
            {
                $route .= '?' . implode('&', $criteria);
            }
        }
        c2cTools::log("redirecting to $route");
        $this->redirect($route);
    }

    protected function filterSearchParameters()
    {
        $out = array();
        
        if($this->getUser()->isConnected())
        {
            $myoutings = $this->getRequestParameter('myoutings', 0);
            if ($myoutings == 1)
            {
                $user_id = $this->getUser()->getId();
                $out[] = "users=$user_id";
            }
        }

        $activities_type = $this->getRequestParameter('acttyp', 1);

        $this->addListParam($out, 'areas');
        $this->addAroundParam($out, 'sarnd');
        
        $this->addNameParam($out, 'onam');
        if ($activities_type == 1)
        {
            $this->addListParam($out, 'act');
        }
        $this->addCompareParam($out, 'oalt');
        $this->addCompareParam($out, 'odif');
        $this->addCompareParam($out, 'olen');

        $this->addNameParam($out, 'snam');
        $this->addCompareParam($out, 'salt');
        $this->addParam($out, 'styp');

        $this->addNameParam($out, 'hnam');
        $this->addCompareParam($out, 'halt');
        $this->addParam($out, 'hsta');

        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');
        $this->addParam($out, 'owtp');

        if ($activities_type == 2)
        {
            $this->addListParam($out, 'act', 'ract');
        }
        $this->addListParam($out, 'sub', '', '', true);
        $this->addFacingParam($out, 'fac');
        $this->addCompareParam($out, 'trat');
        $this->addCompareParam($out, 'sexpo');
        $this->addCompareParam($out, 'lrat');
        $this->addCompareParam($out, 'srat');
        $this->addCompareParam($out, 'grat');
        $this->addCompareParam($out, 'erat');
        $this->addCompareParam($out, 'prat');
        $this->addCompareParam($out, 'irat');
        $this->addCompareParam($out, 'mrat');
        $this->addCompareParam($out, 'frat');
        $this->addCompareParam($out, 'rrat');
        $this->addCompareParam($out, 'arat');
        $this->addCompareParam($out, 'hrat');
        $this->addCompareParam($out, 'wrat');
        $this->addParam($out, 'glac');
        $this->addDateParam($out, 'date');
        
        $this->addParam($out, 'avdate');

        $this->addParam($out, 'geom');
        $this->addListParam($out, 'ocult');

        $this->addParam($out, 'format');

        return $out;
    }

    public function executeConditions()
    {
        $format = $this->getRequestParameter('format', 'cond');
        $format = explode('-', $format);
        if (!in_array('cond', $format))
        {
            $format[] = 'cond';
        }
        $this->format = $format;
        
        self::executeList();
    }

    /**
     * Executes list action, adding ratings from routes linked to outings
     */
    public function executeList()
    {
        // redirect to user outings list if connected and if myoutings criteria is set
        if($this->getUser()->isConnected() && $this->getRequestParameter('myoutings'))
        {
            sfLoader::loadHelpers(array('Pagination'));
            $user_id = $this->getUser()->getId();
            $this->redirect(_addUrlParameters(_getBaseUri(), array('myoutings'), array('users' => $user_id)));
        }
        
        // TODO something to do if outings where filtered on route ratings?
        parent::executeList();

        $format = $this->format;
        if (in_array('cond', $format) && !in_array('json', $format))
        {
            $this->setTemplate('conditions');
            if (in_array('full', $format))
            {
                $this->setPageTitle($this->__('conditions and comments'));
            }
            else
            {
                $this->setPageTitle($this->__('recent conditions'));
            }
        }

        $nb_results = $this->nb_results;
        if ($nb_results == 0) return;

        $show_images = $this->show_images;

        $timer = new sfTimer();
        $outings = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());

        $timer = new sfTimer();
        $outings = Outing::getAssociatedCreatorData($outings); // retrieve outing creator names
        c2cActions::statsdTiming('outing.getAssociatedCreatorData', $timer->getElapsedTime());

        $timer = new sfTimer();
        $outings = Outing::getAssociatedRoutesData($outings); // retrieve associated route ratings
        c2cActions::statsdTiming('outing.getAssociatedRoutesData', $timer->getElapsedTime());

        if (!in_array('list', $format))
        {
            $timer = new sfTimer();
            $outings = Language::getTheBestForAssociatedAreas($outings);
            c2cActions::statsdTiming('language.getTheBestForAssociatedAreas', $timer->getElapsedTime());
        }
        
        // add images infos
        if ($show_images)
        {
            $timer = new sfTimer();
            Image::addAssociatedImages($outings, 'oi');
            c2cActions::statsdTiming('image.addAssociatedImages', $timer->getElapsedTime());
        }

        Area::sortAssociatedAreas($outings);

        $this->items = Language::parseListItems($outings, 'Outing', !$show_images);
    }

    public function handleErrorFilterredirect()
    {
        $this->forward('outings', 'filter');
    }

    public function executeMyOutings()
    {
        // redirect to user outings list if connected
        if($this->getUser()->isConnected())
        {
            $user_id = $this->getUser()->getId();
            $this->redirect('@default?module=outings&action=list&users='.$user_id.'&orderby=date&order=desc');
        }
        else
        {
            sfLoader::loadHelpers('Url');
            $this->redirect(url_for('@login', true).'?redirect=outings/myoutings');
        }
    }

    public function executeMyStats()
    {
        // redirect to user outings list if connected
        if($this->getUser()->isConnected())
        {
            $user_id = $this->getUser()->getId();
            $this->redirect('http://stats.camptocamp.org/user/'.$user_id);
        }
        else
        {
            sfLoader::loadHelpers('Url');
            $this->redirect(url_for('@login', true).'?redirect=outings/mystats');
        }
    }
}
