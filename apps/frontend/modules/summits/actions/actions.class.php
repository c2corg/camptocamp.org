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
        
        $this->associated_summits = array_filter($this->associated_docs, array('c2cTools', 'is_summit'));
        
        // set 2nd param to true to get summit names as well
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, false);
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
                
        $routes = Association::findAllWithBestName($id, $this->getUser()->getCulturesForDocuments(), 'sr');
        
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
    
    /**
     * Overriddes the one in parent class 
     * this is because we sometimes have to do things when centroid coordinates have moved.
     */
    protected function refreshGeoAssociations($id)
    {    
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

        if ($name = $this->getRequestParameter('snam', $this->getRequestParameter('name')))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = '%' . urldecode($name) . '%';
        }

        if ($salt = $this->getRequestParameter('salt'))
        {
            Document::buildCompareCondition($conditions, $values, 'm.elevation', $salt);
        }

        if ($geom = $this->getRequestParameter('geom'))
        {
            Document::buildGeorefCondition($conditions, $geom);
        }

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
        
        return $out;
    }
}
