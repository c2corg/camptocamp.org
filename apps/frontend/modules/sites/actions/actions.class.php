<?php
/**
 * sites module actions.
 *
 * @package    c2corg
 * @subpackage sites
 * @version    $Id: actions.class.php 2455 2007-11-30 11:44:31Z alex $
 */
class sitesActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Site';

    /**
     * Nb of dimensions for geom column
     */   
    protected $geom_dims = 3; 
    // by default, all documents are 3D (X, Y, Z)
    // exceptions are : 
    //      - users and areas : 2D (X, Y)
    //      - outings : 4D (X, Y, Z, T in traces)
    
    /**
     * Additional fields to display in documents lists (additional, relative to id, culture, name)
     * if field comes from i18n table, prefix with 'mi.', else with 'm.' 
     */  
    protected $fields_in_lists = array('m.routes_quantity', 'm.elevation', 'm.rock_types', 'm.site_types');
    
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        $this->associated_parkings = array_filter($this->associated_docs, array('c2cTools', 'is_parking')); 
        $this->associated_huts = array_filter($this->associated_docs, array('c2cTools', 'is_hut')); 
        $this->associated_summits = array_filter($this->associated_docs, array('c2cTools', 'is_summit')); 
        
        $associated_outings = Outing::fetchAdditionalFields(array_filter($this->associated_docs, array('c2cTools', 'is_outing')));
        // sort outings array by antichronological order.
        usort($associated_outings, array('c2cTools', 'cmpDate'));
        $this->associated_outings = $associated_outings;
    }

    /**
     * This function is used to get site specific query paramaters. It is used
     * from the generic action class (in the documents module).
     */
    protected function getQueryParams() {
        $where_array  = array();
        $where_params = array();
        if ($this->hasRequestParameter('min_elevation'))
        {
            $min_elevation = $this->getRequestParameter('min_elevation');
            if (!empty($min_elevation)) {
                $where_array[]  = 'sites.elevation >= ?';
                $where_params[] = $min_elevation;
            }
        }
        if ($this->hasRequestParameter('max_elevation'))
        {
            $max_elevation = $this->getRequestParameter('max_elevation');
            if (!empty($max_elevation)) {
                $where_array[]  = 'sites.elevation <= ?';
                $where_params[] = $max_elevation;
            }
        }
        if ($this->hasRequestParameter('min_routes_quantity'))
        {
            $min_routes_quantity = $this->getRequestParameter('min_routes_quantity');
            if (!empty($min_routes_quantity)) {
                $where_array[]  = 'sites.routes_quantity >= ?';
                $where_params[] = $min_routes_quantity;
            }
        }
        if ($this->hasRequestParameter('max_routes_quantity'))
        {
            $max_routes_quantity = $this->getRequestParameter('max_routes_quantity');
            if (!empty($max_routes_quantity)) {
                $where_array[]  = 'sites.routes_quantity <= ?';
                $where_params[] = $max_routes_quantity;
            }
        }
        if ($this->hasRequestParameter('min_min_rating'))
        {
            $min_min_rating = $this->getRequestParameter('min_min_rating');
            if (!empty($min_min_rating)) {
                $where_array[]  = 'sites.min_rating >= ?';
                $where_params[] = $min_min_rating;
            }
        }
        if ($this->hasRequestParameter('max_min_rating'))
        {
            $max_min_rating = $this->getRequestParameter('max_min_rating');
            if (!empty($max_min_rating)) {
                $where_array[]  = 'sites.min_rating <= ?';
                $where_params[] = $max_min_rating;
            }
        }
        if ($this->hasRequestParameter('min_max_rating'))
        {
            $min_max_rating = $this->getRequestParameter('min_max_rating');
            if (!empty($min_max_rating)) {
                $where_array[]  = 'sites.max_rating >= ?';
                $where_params[] = $min_max_rating;
            }
        }
        if ($this->hasRequestParameter('max_max_rating'))
        {
            $max_max_rating = $this->getRequestParameter('max_max_rating');
            if (!empty($max_max_rating)) {
                $where_array[]  = 'sites.max_rating <= ?';
                $where_params[] = $max_max_rating;
            }
        }
        if ($this->hasRequestParameter('min_mean_rating'))
        {
            $min_mean_rating = $this->getRequestParameter('min_mean_rating');
            if (!empty($min_mean_rating)) {
                $where_array[]  = 'sites.mean_rating >= ?';
                $where_params[] = $min_mean_rating;
            }
        }
        if ($this->hasRequestParameter('max_mean_rating'))
        {
            $max_mean_rating = $this->getRequestParameter('max_mean_rating');
            if (!empty($max_mean_rating)) {
                $where_array[]  = 'sites.mean_rating <= ?';
                $where_params[] = $max_mean_rating;
            }
        }
        if ($this->hasRequestParameter('min_min_height'))
        {
            $min_min_height = $this->getRequestParameter('min_min_height');
            if (!empty($min_min_height)) {
                $where_array[]  = 'sites.min_height >= ?';
                $where_params[] = $min_min_height;
            }
        }
        if ($this->hasRequestParameter('max_min_height'))
        {
            $max_min_height = $this->getRequestParameter('max_min_height');
            if (!empty($max_min_height)) {
                $where_array[]  = 'sites.min_height <= ?';
                $where_params[] = $max_min_height;
            }
        }
        if ($this->hasRequestParameter('min_max_height'))
        {
            $min_max_height = $this->getRequestParameter('min_max_height');
            if (!empty($min_max_height)) {
                $where_array[]  = 'sites.max_height >= ?';
                $where_params[] = $min_max_height;
            }
        }
        if ($this->hasRequestParameter('max_max_height'))
        {
            $max_max_height = $this->getRequestParameter('max_max_height');
            if (!empty($max_max_height)) {
                $where_array[]  = 'sites.max_height <= ?';
                $where_params[] = $max_max_height;
            }
        }
        if ($this->hasRequestParameter('min_mean_height'))
        {
            $min_mean_height = $this->getRequestParameter('min_mean_height');
            if (!empty($min_mean_height)) {
                $where_array[]  = 'sites.mean_height >= ?';
                $where_params[] = $min_mean_height;
            }
        }
        if ($this->hasRequestParameter('max_mean_height'))
        {
            $max_mean_height = $this->getRequestParameter('max_mean_height');
            if (!empty($max_mean_height)) {
                $where_array[]  = 'sites.mean_height <= ?';
                $where_params[] = $max_mean_height;
            }
        }
        if ($this->hasRequestParameter('climbing_styles'))
        {
            $climbing_styles = $this->getRequestParameter('climbing_styles');
            $where = $this->getWhereClause(
                $climbing_styles, 'mod_sites_climbing_styles_list', '? = ANY (sites.climbing_styles)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('rock_types'))
        {
            $rock_types = $this->getRequestParameter('rock_types');
            $where = $this->getWhereClause(
                $rock_types, 'mod_sites_rock_types_list', '? = ANY (sites.rock_types)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('site_types'))
        {
            $site_types = $this->getRequestParameter('site_types');
            $where = $this->getWhereClause(
                $site_types, 'mod_sites_site_types_list', '? = ANY (sites.site_types)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('children_proof'))
        {
            $children_proofs = $this->getRequestParameter('children_proof');
            $where = $this->getWhereClause(
                $children_proofs, 'mod_sites_children_proof_list', 'sites.children_proof = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('rain_proof'))
        {
            $rain_proofs = $this->getRequestParameter('rain_proof');
            $where = $this->getWhereClause(
                $rain_proofs, 'mod_sites_rain_proof_list', 'sites.rain_proof = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('facings'))
        {
            $facings = $this->getRequestParameter('facings');
            $where = $this->getWhereClause(
                $facings, 'mod_sites_facings_list', '? = ANY (sites.facings)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('equipment_rating'))
        {
            $equipment_ratings = $this->getRequestParameter('equipment_rating');
            $where = $this->getWhereClause(
                $equipment_ratings, 'mod_sites_equipment_ratings_list', 'sites.equipment_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('best_periods'))
        {
            $best_periods = $this->getRequestParameter('best_periods');
            $where = $this->getWhereClause(
                $best_periods, 'mod_sites_best_periods_list', '? = ANY (sites.best_periods)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        $params = array(
            'select' => array(
                'sites.elevation',
                'sites.routes_quantity',
                'sites.min_rating',
                'sites.max_rating',
                'sites.mean_rating',
                'sites.min_height',
                'sites.max_height',
                'sites.mean_height',
                'sites.climbing_styles',
                'sites.rock_types',
                'sites.facings',
                'sites.equipment_rating',
                'sites.best_periods'
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
        
        $list = sfConfig::get('mod_sites_rock_free_ratings_list');

        $elevation = isset($result['elevation']) ? $result['elevation'] : '-';
        $routes_quantity = isset($result['routes_quantity']) ? $result['routes_quantity'] : '-';
        $min_rating = isset($result['min_rating']) ? $this->__($list[$result['min_rating']]) : '-';
        $max_rating = isset($result['max_rating']) ? $this->__($list[$result['max_rating']]) : '-';

        $html  = '<td>' . link_to($result['name'], '@document_by_id?module=sites&id=' . $result['id']) . '</td>';
        $html .= '<td>' . $elevation . '</td>';
        $html .= '<td>' . $routes_quantity . '</td>';
        $html .= '<td>' . $min_rating . '</td>';
        $html .= '<td>' . $max_rating . '</td>';

        return $html;
    }
    
    /**
     * Overriddes the one in parent class 
     * this is because we sometimes have to do things when centroid coordinates have moved.
     */
    protected function refreshGeoAssociations($id)
    {    
        c2cTools::log("Entering refreshGeoAssociations for outings linked with site $id");
        
        $associated_outings = Association::findAllAssociatedDocs($id, array('id', 'geom_wkt'), 'to');
        
        if (count($associated_outings))
        {
            $geoassociations = GeoAssociation::findAllAssociations($id, null, 'main');
            // we create new associations :
            //  (and delete old associations before creating the new ones)
            //  (and do not create outings-maps associations)        
            foreach ($associated_outings as $outing)
            {
                $i = $outing['id'];
            
                if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                {
                    // replicate geoassoces from doc $id to outing $i and delete previous ones 
                    // (because there might be geoassociations created by this same process)
                    $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, false);
                    c2cTools::log("created $nb_created geo associations for outing N° $i");
                    $this->clearCache('outings', $i, false, 'view');
                }
            }
        }
    }
}
