<?php
/**
 * summits module actions.
 *
 * @package    c2corg
 * @subpackage summits
 * @version    $Id: actions.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class summitsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Summit';
    
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        
        if (!$this->document->isArchive())
        {
            $this->associated_summits = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_summit')));
            
            if (!empty($this->associated_summits))
            {
                $elevation = $this->document->get('elevation');
                $sub_summits = array();
                foreach ($this->associated_summits as $summit)
                {
                    if ($summit['elevation'] <= $elevation)
                    {
                        $sub_summits[] = $summit['id'];
                    }
                }
                
                if(!empty($sub_summits))
                {
                    $user = $this->getUser();
                    $prefered_cultures = $user->getCulturesForDocuments();
                    $associated_summit_routes = findWithBestName($sub_summits, $prefered_cultures, 'sr', true);
                    $this->associated_docs = array_merge($this->associated_docs, $associated_summit_routes);
                }
            }
            
            // second param will not display the summit name before the route when the summit is the one of the document
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ', $this->document->get('id'));
    
            $description = array($this->__('summit') . ' :: ' . $this->document->get('name'),
                                 $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePopup()
    {
        parent::executePopup();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ', $this->document->get('id'));
    }

    /**
     * This function is used to get summit specific query paramaters. It is used
     * from the generic action class (in the documents module).
     */
    protected function getQueryParams() {
        $where_array  = array();
        $where_params = array();
        if ($this->hasRequestParameter('min_elevation'))
        {
            $min_elevation = $this->getRequestParameter('min_elevation');
            if (!empty($min_elevation)) {
                $where_array[]  = 'summits.elevation >= ?';
                $where_params[] = $min_elevation;
            }
        }
        if ($this->hasRequestParameter('max_elevation'))
        {
            $max_elevation = $this->getRequestParameter('max_elevation');
            if (!empty($max_elevation)) {
                $where_array[]  = 'summits.elevation <= ?';
                $where_params[] = $max_elevation;
            }
        }
        $params = array(
            'select' => array('summits.elevation'),
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
        
        $html  = '<td>' . link_to($result['name'], '@document_by_id?module=summits&id=' . $result['id']) . '</td>';
        $html .= '<td>' . $result['elevation'] . '</td>';

        return $html;
    }
    
    /**
     * returns a list of associated routes
     */
    public function executeGetroutes()
    {
        $id = $this->getRequestParameter('summit_id');
        $div_id = $this->getRequestParameter('div_id');
             
        $user = $this->getUser();
        $user_id = $user->getId(); 
        
        // if id = 0 or no provided
        if (!$id)
        {
            return $this->ajax_feedback('Missing id parameter');
        }
        
        // if session is time-over
        if (!$user_id)
        {
            return $this->ajax_feedback('Your session is over. Please login again.');
        }
        
        $summit = Document::find('Summit', $id, array('id')); 
        if (!$summit)
        {
            return $this->ajax_feedback('Summit not found'); 
        }
                
        $routes = c2cTools::sortArrayByName(Association::findAllWithBestName($id, $this->getUser()->getCulturesForDocuments(), 'sr'));
        
        $msg = $this->__('No associated route found');
        if (count($routes) == 0) return $this->ajax_feedback("<option value='0'>$msg</option>");

        if (!$div_id)
        {
            return $this->ajax_feedback('Please chose a "select" container ID in "remote_function"');
        }

        $output = '<select id="' . $div_id . '" name="' . $div_id . '" onchange="getWizardRouteRatings();">';
        foreach ($routes as $route)
        {
            $output .= '<option value="' . $route['id'] . '">' . $route['name'] . '</option>';
        }
        $output .= '</select>';
        
        return $this->renderText($output);
    }

    /** summits: refresh geo associations of 'sub' routes and outings */
    public function executeRefreshgeoassociations()
    {
        $referer = $this->getRequest()->getReferer();
        $id = $this->getRequestParameter('id');

        // check if user is moderator: done in apps/frontend/config/security.yml

        $this->document = Document::find($this->model_class, $id, array('summit_type'));

        if (!$this->document)
        {
            $this->setErrorAndRedirect('Document does not exist', $referer);
        }

        $nb_created = gisQuery::createGeoAssociations($id, true, true);
        c2cTools::log("created $nb_created geo associations");

        $this->refreshGeoAssociations($id);

        $this->setNoticeAndRedirect('Geoassociations refreshed', "@document_by_id?module=summits&id=$id");
    }
    
    /**
     * Overriddes the one in parent class 
     * this is because we sometimes have to do things when centroid coordinates have moved.
     */
    protected function refreshGeoAssociations($id)
    {    
        // don't refresh associated doc if summit type is "raid"
        if ($this->document->get('summit_type') == 5)
            return;
        
        c2cTools::log("Entering refreshGeoAssociations for routes linked with summit $id");
        
        $associated_routes = Association::findAllAssociatedDocs($id, array('id', 'geom_wkt'), 'sr');
        
        if (count($associated_routes))
        {
            $geoassociations = GeoAssociation::findAllAssociations($id, null, 'main'); 
            // we create new associations :
            //  (and delete old associations before creating the new ones)
            //  (and do not create outings-maps associations)        
            foreach ($associated_routes as $route)
            {
                $i = $route['id'];
            
                if (!$route['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                {
                    // replicate geoassoces from doc $id to outing $i and delete previous ones 
                    // (because there might be geoassociations created by this same process)
                    $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, true);
                    c2cTools::log("created $nb_created geo associations for route N° $i");
                    $this->clearCache('routes', $i, false, 'view');
 
                    $associated_outings = Association::findAllAssociatedDocs($i, array('id', 'geom_wkt'), 'ro');
        
                    if (count($associated_outings))
                    {
                        $geoassociations2 = GeoAssociation::findAllAssociations($i, null, 'main'); 
                        // we create new associations :
                        //  (and delete old associations before creating the new ones)
                        //  (and do not create outings-maps associations)        
                        foreach ($associated_outings as $outing)
                        {
                            $j = $outing['id'];
            
                            if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                            {
                                // replicate geoassoces from doc $id to outing $i and delete previous ones 
                                // (because there might be geoassociations created by this same process)
                                $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations2, $j, true, false);
                                c2cTools::log("created $nb_created geo associations for outing N° $j");
                                $this->clearCache('outings', $j, false, 'view');
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Get list of criteria used to filter items list.
     * Overriddes the one in parent class.
     * @return array
     */
     
    protected function getListCriteria()
    {
        $conditions = $values = array();

        if ($areas = $this->getRequestParameter('areas'))
        {
            Document::buildListCondition($conditions, $values, 'ai.id', $areas);
        }
        elseif ($bbox = $this->getRequestParameter('bbox'))
        {
            Document::buildBboxCondition($conditions, $values, 'm.geom', $bbox);
        }

        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('snam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'salt');
        $this->buildCondition($conditions, $values, 'List', 'm.summit_type', 'styp');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'snam': return 'mi.search_name';
            case 'salt': return 'm.elevation';
            case 'styp': return 'm.summit_type';
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            default: return NULL;
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
        $this->addNameParam($out, 'snam');
        $this->addCompareParam($out, 'salt');
        $this->addParam($out, 'geom');
        $this->addParam($out, 'bbox');
        $this->addListParam($out, 'styp');
        
        return $out;
    }
}
