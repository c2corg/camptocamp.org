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
        sfLoader::loadHelpers('Date');
        $title = $this->__('outing') . ' :: ' . format_date($this->document->get('date'), 'D')
                 . ', ' . $this->document->get('name');
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
            
            $parent_ids = array();
            $associated_summits = array();
            $associated_huts = array();
            $associated_parkings = array();
            if (count($associated_routes))
            {
                foreach ($associated_routes as $route)
                {
                    if ($route['duration'] <= 4)
                    {
                        $parent_ids[] = $route['id'];
                    }
                }
            }
            if (count($this->associated_sites))
            {
                foreach ($this->associated_sites as $site)
                {
                    $parent_ids[] = $site['id'];
                }
            }
            
            if (count($parent_ids))
            {
                $associated_route_docs = Association::findWithBestName($parent_ids, $prefered_cultures, array('sr', 'hr', 'pr', 'pt'), false, false);
                if (count($associated_route_docs))
                {
                    $associated_route_docs = c2cTools::sortArray($associated_route_docs, 'elevation');
                    $associated_summits = array_filter($associated_route_docs, array('c2cTools', 'is_summit'));
                    $associated_huts = array_filter($associated_route_docs, array('c2cTools', 'is_hut'));
                    $associated_parkings = Parking::getAssociatedParkingsData(array_filter($associated_route_docs, array('c2cTools', 'is_parking')));
                }
            }
            
            $this->associated_summits = $associated_summits;
            $this->associated_huts = $associated_huts;
            $this->associated_parkings = $associated_parkings;
            $this->associated_routes = $associated_routes;
            
            $associated_users = array_filter($this->associated_docs, array('c2cTools', 'is_user'));
            $first_user = array_pop($associated_users);
            if (count($associated_users) >= 2)
            {
                $associated_users = c2cTools::sortArrayByName($associated_users);
            }
            array_unshift($associated_users, $first_user);
            $this->associated_users = $associated_users;
    
            $description = array($title, $this->getActivitiesList(), $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
        else
        {
            // only moderators and associated users should see archive versions of outings
            $this->filterAuthorizedPeople($id);
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

    protected function endEdit()
    {
        //Test if form is submitted or not
        if ($this->success) // form submitted and success (doc has been saved)
        {
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
                        // clear cache of associated route ...
                        $this->clearCache('sites', $associated_id, false, 'view');
                    }
                    
                    // here if we have created a new document and if $this->document->get('geom_wkt') is null, then use associated doc geom associations:
                    // this allows us to filter on ranges even if no GPX is uploaded
                    if ($this->new_document && $associated_id && !$this->document->get('geom_wkt'))
                    {
                        // get all associated regions (only regions, countries, depts, no maps !) with this summit:
                        $associations = GeoAssociation::findAllAssociations($associated_id, array('dr', 'dc', 'dd'));
                        // replicate them with outing_id instead of (route_id or site_id):
                        foreach ($associations as $ea)
                        {
                            $a = new GeoAssociation();
                            $a->doSaveWithValues($id, $ea->get('linked_id'), $ea->get('type'));
                        }
                    }
                    
                }
            }    

            // create also association with current user.
            if ($this->new_document)
            {
                $uo = new Association();
                $uo->doSaveWithValues($user_id, $id, 'uo', $user_id); // main, linked, type
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
     * Overloaded method from documentsActions class.
     */
    protected function isUnModified()
    {
        $modified = array();
        // if there is a gpx file attached, we get lat, lon, max_elevation etc... with doctrine null values
        // we do not want to count them
        foreach ($this->document->getModified() as $key => $item)
        {
            if (!$item instanceof Doctrine_Null)
            {
                $modified[$key] = $item;
            }
        }
        return (count($modified) == 0 &&
                count($this->document->getCurrentI18nObject()->getModified()) == 0);
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
        if (!$linked_doc)
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
    
    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'onam': return 'mi.search_name';
            case 'act':  return 'm.activities';
            case 'alt':  return 'm.max_elevation';
            case 'date': return 'm.date';
            case 'hdif': return 'm.height_diff_up';
            case 'anam': return 'ai.name';
            case 'cond': return 'm.conditions_status';
            case 'geom': return 'm.geom_wkt';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {   
        $conditions = $values = array();

        // outing criteria
        $this->buildCondition($conditions, $values, 'List', 'ai.id', 'areas');
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('onam', 'name'));
        $this->buildCondition($conditions, $values, 'Array', 'o.activities', 'act');
        $this->buildCondition($conditions, $values, 'Compare', 'm.max_elevation', 'oalt');
        $this->buildCondition($conditions, $values, 'Compare', 'm.height_diff_up', 'odif');
        $this->buildCondition($conditions, $values, 'Compare', 'm.outing_length', 'olen');
        $this->buildCondition($conditions, $values, 'Date', 'date', 'date');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');
        $this->buildCondition($conditions, $values, 'Bool', 'm.outing_with_public_transportation', 'owtp');
        $this->buildCondition($conditions, $values, 'Bool', 'm.partial_trip', 'ptri');

        // summit criteria
        $this->buildCondition($conditions, $values, 'String', 'si.search_name', 'snam', 'join_summit', true);
        $this->buildCondition($conditions, $values, 'Compare', 's.elevation', 'salt', 'join_summit');
        $this->buildCondition($conditions, $values, 'List', 's.summit_type', 'styp', 'join_summit');
        $this->buildCondition($conditions, $values, 'List', 's.id', 'summit', 'join_summit');

        // hut criteria
        $this->buildCondition($conditions, $values, 'String', 'hi.search_name', 'hnam', 'join_hut', true);
        $this->buildCondition($conditions, $values, 'Compare', 'h.elevation', 'halt', 'join_hut');
        $this->buildCondition($conditions, $values, 'Bool', 'h.is_staffed', 'hsta', 'join_hut');
        $this->buildCondition($conditions, $values, 'List', 'h.id', 'hut', 'join_hut');

        // parking criteria
        $this->buildCondition($conditions, $values, 'String', 'pi.search_name', 'pnam', 'join_parking', true);
        $this->buildCondition($conditions, $values, 'Compare', 'p.elevation', 'palt', 'join_parking');
        $this->buildCondition($conditions, $values, 'List', 'p.public_transportation_rating', 'tp', 'join_parking');
        $this->buildCondition($conditions, $values, 'Array', 'p.public_transportation_types', 'tpty', 'join_parking');
        $this->buildCondition($conditions, $values, 'List', 'p.id', 'parking', 'join_parking');

        // route criteria
        $this->buildCondition($conditions, $values, 'String', 'ri.search_name', 'rnam', 'join_route', true);
        $this->buildCondition($conditions, $values, 'Facing', 'r.facing', 'fac', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.toponeige_technical_rating', 'trat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.toponeige_exposition_rating', 'expo', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.labande_global_rating', 'lrat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.labande_ski_rating', 'srat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.ice_rating', 'irat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.mixed_rating', 'mrat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.rock_free_rating', 'frat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.rock_required_rating', 'rrat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.aid_rating', 'arat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.global_rating', 'grat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.engagement_rating', 'erat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.hiking_rating', 'hrat', 'join_route');
        $this->buildCondition($conditions, $values, 'Compare', 'r.equipment_rating', 'prat', 'join_route');
        $this->buildCondition($conditions, $values, 'Array', 'r.sub_activities', 'sub', 'join_route');
        $this->buildCondition($conditions, $values, 'Bool', 'r.is_on_glacier', 'glac', 'join_route');
        $this->buildCondition($conditions, $values, 'List', 'r.id', 'route', 'join_route');

        // site criteria
        $this->buildCondition($conditions, $values, 'List', 'l5.main_id', 'site', 'join_site');

        // user criteria
        $this->buildCondition($conditions, $values, 'List', 'l6.main_id', 'user', 'join_user');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addNameParam($out, 'onam');
        $this->addListParam($out, 'areas');

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

        $this->addNameParam($out, 'rnam');
        $this->addCompareParam($out, 'odif');
        $this->addFacingParam($out, 'fac');
        $this->addListParam($out, 'act');
        $this->addCompareParam($out, 'trat');
        $this->addCompareParam($out, 'expo');
        $this->addCompareParam($out, 'lrat');
        $this->addCompareParam($out, 'srat');
        $this->addCompareParam($out, 'irat');
        $this->addCompareParam($out, 'mrat');
        $this->addCompareParam($out, 'frat');
        $this->addCompareParam($out, 'rrat');
        $this->addCompareParam($out, 'arat');
        $this->addCompareParam($out, 'grat');
        $this->addCompareParam($out, 'erat');
        $this->addCompareParam($out, 'hrat');
        $this->addCompareParam($out, 'olen');
        $this->addCompareParam($out, 'prat');
        $this->addParam($out, 'glac');
        $this->addListParam($out, 'sub');
        $this->addDateParam($out, 'date');

        $this->addParam($out, 'geom');

        return $out;
    }

    public function executeConditions()
    {
        $this->pager = Outing::browse($this->getListSortCriteria(10),
                                      $this->getListCriteria(),
                                      true);
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();
        $this->setPageTitle($this->__('recent conditions'));

        $outings = $this->pager->getResults('array');

        if (count($outings) == 0) return;

        $outings = Outing::getAssociatedRoutesData($outings); // retrieve associated route ratings
        $outings = Language::getTheBestForAssociatedAreas($outings);
        $this->items = Language::parseListItems($outings, 'Outing');
    }

    /**
     * Executes list action, adding ratings from routes linked to outings
     */
    public function executeList()
    {
        // TODO something to do if outings where filtered on route ratings?

        parent::executeList();

        $outings = $this->pager->getResults('array');

        if (count($outings) == 0) return;
        
        $outings = Outing::getAssociatedRoutesData($outings); // retrieve associated route ratings
        $this->items = Language::parseListItems($outings, 'Outing');
    }
}
