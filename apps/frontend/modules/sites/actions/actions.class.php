<?php
/**
 * sites module actions.
 *
 * @package    c2corg
 * @subpackage sites
 * @version    $Id: actions.class.php 2541 2007-12-20 18:17:11Z alex $
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
    
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();
            $current_doc_id = $this->getRequestParameter('id');
            $parent_ids = array();
            
            $associated_sites = $this->associated_sites;
            if (count($associated_sites))
            {
                foreach ($associated_sites as $site)
                {
                    $parent_ids[] = $site['id'];
                }
                $associated_sites = Association::addChildWithBestName($associated_sites, $prefered_cultures, 'tt');
            }
            
            $associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            if (count($associated_parkings))
            {
                foreach ($associated_parkings as $parking)
                {
                    $parent_ids[] = $parking['id'];
                }
            }
            
            if (count($parent_ids))
            {
                $associated_childs = Association::findWithBestName($parent_ids, $prefered_cultures, array('tt', 'pp'), true, true, $current_doc_id);
            
                if (count($associated_sites))
                {
                    $associated_sites = Association::addChild($associated_sites, array_filter($associated_childs, array('c2cTools', 'is_site')), 'tt');
                }
            
                if (count($associated_parkings))
                {
                    $associated_parkings = Association::addChild($associated_parkings, array_filter($associated_childs, array('c2cTools', 'is_parking')), 'pp');
                }
            }

            $this->associated_sites = $associated_sites;
            $this->associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
            
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_huts = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_hut')), 'elevation');
            $this->associated_summits = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_summit')));
            
            $associated_outings = Outing::fetchAdditionalFields(array_filter($this->associated_docs, array('c2cTools', 'is_outing')), true);
            // sort outings array by antichronological order.
            usort($associated_outings, array('c2cTools', 'cmpDate'));
            $this->nb_outings = count($associated_outings);
            // group them by blocks
            $outings_limit = sfConfig::get('app_documents_outings_limit');
            $a = array();
            $i = 0;
            while (count($associated_outings) - $i*$outings_limit > $outings_limit)
            {
                $a[] = array_slice($associated_outings, $i * $outings_limit, $outings_limit);
                $i++;
            }
            $a[] = array_slice($associated_outings, $i * $outings_limit);
            $this->associated_outings = $a;
    
            $description = array($this->__('site') . ' :: ' . $this->document->get('name'),
                                 $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    /** refresh geoassociations of the route and 'sub' outings */
    public function executeRefreshgeoassociations()
    {
        $referer = $this->getRequest()->getReferer();
        $id = $this->getRequestParameter('id');

        // check if user is moderator: done in apps/frontend/config/security.yml

        if (!Document::checkExistence($this->model_class, $id))
        {
            $this->setErrorAndRedirect('Document does not exist', $referer);
        }

        $nb_created = gisQuery::createGeoAssociations($id, true, true);
        c2cTools::log("created $nb_created geo associations");

        $this->refreshGeoAssociations($id);

        $this->setNoticeAndRedirect('Geoassociations refreshed', "@document_by_id?module=sites&id=$id");
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
                $site_types, 'app_sites_site_types', '? = ANY (sites.site_types)');
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
                $equipment_ratings, 'app_equipment_ratings_list', 'sites.equipment_rating = ?');
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

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'snam': return 'mi.search_name';
            case 'salt': return 'm.elevation';
            case 'rqty': return 'm.routes_quantity';
            case 'styp': return 'm.site_types';
            case 'rtyp': return 'm.rock_types';
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        $this->buildCondition($conditions, $values, 'List', 'ai.id', 'areas');

        // parking criteria
        $this->buildCondition($conditions, $values, 'String', 'pi.search_name', 'pnam', 'join_parking', true);
        $this->buildCondition($conditions, $values, 'Compare', 'p.elevation', 'palt', 'join_parking');
        $this->buildCondition($conditions, $values, 'List', 'p.public_transportation_rating', 'tp', 'join_parking');
        $this->buildCondition($conditions, $values, 'Array', 'p.public_transportation_types', 'tpty', 'join_parking');

        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('snam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'salt');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');
        $this->buildCondition($conditions, $values, 'Array', 's.site_types', 'styp');
        $this->buildCondition($conditions, $values, 'Array', 's.climbing_styles', 'csty');
        $this->buildCondition($conditions, $values, 'Compare', 'm.equipment_rating', 'prat');
        $this->buildCondition($conditions, $values, 'Compare', 'm.routes_quantity', 'rqua');
        $this->buildCondition($conditions, $values, 'Compare', 'm.mean_height', 'mhei');
        $this->buildCondition($conditions, $values, 'Compare', 'm.mean_rating', 'mrat');
        $this->buildCondition($conditions, $values, 'Array', 's.facings', 'fac');
        $this->buildCondition($conditions, $values, 'Array', 's.rock_types', 'rock');
        $this->buildCondition($conditions, $values, 'List', 'm.children_proof', 'chil');
        $this->buildCondition($conditions, $values, 'List', 'm.rain_proof', 'rain');

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
        
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');

        $this->addNameParam($out, 'snam');
        $this->addCompareParam($out, 'salt');
        $this->addListParam($out, 'styp');
        $this->addListParam($out, 'csty');
        $this->addCompareParam($out, 'prat');
        $this->addCompareParam($out, 'rqua');
        $this->addCompareParam($out, 'mhei');
        $this->addCompareParam($out, 'mrat');
        $this->addListParam($out, 'fac');
        $this->addListParam($out, 'rock');
        $this->addListParam($out, 'chil');
        $this->addListParam($out, 'rain');
        $this->addParam($out, 'geom');

        return $out;
    }
}
