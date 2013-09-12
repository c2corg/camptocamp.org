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
                    // pour les docs de 2ème niveau, on retient uniquement les itinéraires de 1 ou 2 jours
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
                // s'il n'y a pas d'itinéraire de 1 ou 2 jours, on utilise les itinéraires qui ont la plus petite durée
                // s'il n'y en a pas non plus, on utilise les itinéraire dont la durée est non renseignée
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
                    $this->setErrorAndRedirect('You cannot create an outing without linking it to an existing route or site', '@default_index?module=outings');
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
        
        $linked_doc = Document::find('Route', $id, array('id', 'module'));
        if ($linked_doc)
        {
            if ($this->document)
            {
	            $linked_doc->set('name', $this->document->get('name'));
            }
        }
        else
        {
            $linked_doc = Document::find('Site', $id, array('id', 'module'));
        }
        
        if (!$linked_doc)
        {
            $this->setErrorAndRedirect('You cannot create an outing without linking it to an existing route or site', '@default_index?module=outings');
        }
    }
    
    /**
     * This function is used to get outing specific query paramaters. It is used
     * from the generic action class (in the documents module).
     */
    protected function getQueryParams() {
        $where_array  = array();
        $where_params = array();
        if ($this->hasRequestParameter('min_min_elevation'))
        {
            $min_min_elevation = $this->getRequestParameter('min_min_elevation');
            if (!empty($min_min_elevation)) {
                $where_array[]  = 'outings.min_elevation >= ?';
                $where_params[] = $min_min_elevation;
            }
        }
        if ($this->hasRequestParameter('max_min_elevation'))
        {
            $max_min_elevation = $this->getRequestParameter('max_min_elevation');
            if (!empty($max_min_elevation)) {
                $where_array[]  = 'outings.min_elevation <= ?';
                $where_params[] = $max_min_elevation;
            }
        }
        if ($this->hasRequestParameter('min_max_elevation'))
        {
            $min_max_elevation = $this->getRequestParameter('min_max_elevation');
            if (!empty($min_max_elevation)) {
                $where_array[]  = 'outings.max_elevation >= ?';
                $where_params[] = $min_max_elevation;
            }
        }
        if ($this->hasRequestParameter('max_max_elevation'))
        {
            $max_max_elevation = $this->getRequestParameter('max_max_elevation');
            if (!empty($max_max_elevation)) {
                $where_array[]  = 'outings.max_elevation <= ?';
                $where_params[] = $max_max_elevation;
            }
        }
        if ($this->hasRequestParameter('min_height_diff_up'))
        {
            $min_height_diff_up = $this->getRequestParameter('min_height_diff_up');
            if (!empty($min_height_diff_up)) {
                $where_array[]  = 'outings.height_diff_up >= ?';
                $where_params[] = $min_height_diff_up;
            }
        }
        if ($this->hasRequestParameter('max_height_diff_up'))
        {
            $max_height_diff_up = $this->getRequestParameter('max_height_diff_up');
            if (!empty($max_height_diff_up)) {
                $where_array[]  = 'outings.height_diff_up <= ?';
                $where_params[] = $max_height_diff_up;
            }
        }
        if ($this->hasRequestParameter('activities'))
        {
            $activities = $this->getRequestParameter('activities');
            $where = $this->getWhereClause(
                $activities, 'app_activities_list', '? = ANY (outings.activities)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('date_from') && $this->hasRequestParameter('date_to'))
        {
            $date_from = $this->getRequestParameter('date_from');
            $date_to = $this->getRequestParameter('date_to');
            $where_array[] = 'outings.date >= ? AND outings.date <= ?';
            $where_params[] = $date_from['year'] . '-' . $date_from['month'] . '-' . $date_from['day'];
            $where_params[] = $date_to['year'] . '-' . $date_to['month'] . '-' . $date_to['day'];
        }
        if ($this->hasRequestParameter('max_height_diff_down'))
        {
            $max_height_diff_down = $this->getRequestParameter('max_height_diff_down');
            if (!empty($max_height_diff_down)) {
                $where_array[]  = 'outings.height_diff_down <= ?';
                $where_params[] = $max_height_diff_down;
            }
        }
        if ($this->hasRequestParameter('min_outing_length'))
        {
            $min_outing_length = $this->getRequestParameter('min_outing_length');
            if (!empty($min_outing_length)) {
                $where_array[]  = 'outings.outing_length >= ?';
                $where_params[] = $min_outing_length;
            }
        }
        if ($this->hasRequestParameter('max_outing_length'))
        {
            $max_outing_length = $this->getRequestParameter('max_outing_length');
            if (!empty($max_outing_length)) {
                $where_array[]  = 'outings.outing_length <= ?';
                $where_params[] = $max_outing_length;
            }
        }
        if ($this->hasRequestParameter('hut_status'))
        {
            $hut_statuss = $this->getRequestParameter('hut_status');
            $where = $this->getWhereClause(
                $hut_statuss, 'mod_outings_hut_statuses_list', 'outings.hut_status = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('frequentation_status'))
        {
            $frequentation_statuss = $this->getRequestParameter('frequentation_status');
            $where = $this->getWhereClause(
                $frequentation_statuss, 'mod_outings_frequentation_statuses_list', 'outings.frequentation_status = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('conditions_status'))
        {
            $conditions_statuss = $this->getRequestParameter('conditions_status');
            $where = $this->getWhereClause(
                $conditions_statuss, 'mod_outings_conditions_statuses_list', 'outings.conditions_status = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('access_status'))
        {
            $access_statuss = $this->getRequestParameter('access_status');
            $where = $this->getWhereClause(
                $access_statuss, 'mod_outings_access_statuses_list', 'outings.access_status = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('lift_status'))
        {
            $lift_statuss = $this->getRequestParameter('lift_status');
            $where = $this->getWhereClause(
                $lift_statuss, 'mod_outings_lift_statuses_list', 'outings.lift_status = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('glacier_status'))
        {
            $glacier_statuss = $this->getRequestParameter('glacier_status');
            $where = $this->getWhereClause(
                $glacier_statuss, 'mod_outings_glacier_statuses_list', 'outings.glacier_status = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('track_status'))
        {
            $track_statuss = $this->getRequestParameter('track_status');
            $where = $this->getWhereClause(
                $track_statuss, 'mod_outings_track_statuses_list', 'outings.track_status = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        $params = array(
            'select' => array(
                'outings.min_elevation',
                'outings.max_elevation',
                'outings.height_diff_up',
                'outings.activities',
                'outings.date',
                'outings.height_diff_down',
                'outings.outing_length',
                'outings.hut_status',
                'outings.frequentation_status',
                'outings.conditions_status',
                'outings.access_status',
                'outings.lift_status',
                'outings.glacier_status',
                'outings.track_status'
            ),
            'where'  => array(
                'where_array'  => $where_array,
                'where_params' => $where_params
            )
        );
        return $params; 
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
        if ($this->getRequestParameter('cond'))
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
        $this->addCompareParam($out, 'expo');
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

        $this->addParam($out, 'geom');
        $this->addListParam($out, 'ocult');

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
        // TODO something to do if outings where filtered on route ratings?
        parent::executeList();

        $format = $this->format;
        if (in_array('cond', $format))
        {
            $this->setTemplate('conditions');
            $this->setPageTitle($this->__('recent conditions'));
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
