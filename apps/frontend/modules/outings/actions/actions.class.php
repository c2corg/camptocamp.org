<?php
/**
 * outings module actions.
 *
 * @package    c2corg
 * @subpackage outings
 * @version    $Id: actions.class.php 2386 2007-11-20 15:19:54Z fvanderbiest $
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
    // by default, all documents are 3D (X, Y, Z)
    // exceptions are : 
    //      - users and areas : 2D (X, Y)
    //      - outings : 4D (X, Y, Z, T in traces)
    
    /**
     * Additional fields to display in documents lists (additional, relative to id, culture, name)
     * if field comes from i18n table, prefix with 'mi.', else with 'm.' 
     */  
    protected $fields_in_lists = array('m.activities', 'm.date', 'm.height_diff_up', 'v.version', 'hm.user_id',
                                       'u.name_to_use', 'u.private_name', 'u.username', 'u.login_name');
    
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        
        $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route')); 
        $this->associated_routes = Route::addBestSummitName($associated_routes);
        
        $associated_users = array_filter($this->associated_docs, array('c2cTools', 'is_user')); 
        // here, we should get the best name to use for users and use it instead of classic "name" field in associated_docs array passed to templates.
        $this->associated_users = UserPrivateData::replaceNameToUse($associated_users);            
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
                    $linked_doc = Document::find('Route', $this->getRequestParameter('link'), array('activities', 'height_diff_up', 'height_diff_down'));

                    $linked_doc->setBestCulture($prefered_cultures);
                    $this->linked_doc = $linked_doc;
            
                    // FIXME: this "field getting" triggers an additional request to the db (yet already hydrated), 
                    // probably because activities field has been hydrated into object as a string and not as an array
                    // cf filterGetActivities and filterSetActivities in Route.class.php ...
                    $activities = $linked_doc->get('activities');
                    $document->set('activities', $activities);
            
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
                    $document->set('name', $this->highest_summit_name . ' : ' . $linked_doc->get('name'));
                    
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
}
