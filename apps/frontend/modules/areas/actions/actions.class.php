<?php
/**
 * areas module actions.
 *
 * @package    c2corg
 * @subpackage areas
 * @version    $Id: actions.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class areasActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Area';
    
    /**
     * Nb of dimensions for geom column
     */   
    protected $geom_dims = 2; 

    /**
     * This function is used to get summit specific query paramaters. It is used
     * from the generic action class (in the documents module).
     */
    protected function getQueryParams() {
        $where_array  = array();
        $where_params = array();
        if ($this->hasRequestParameter('area_type'))
        {
            $area_types = $this->getRequestParameter('area_type');
            $list = sfConfig::get('mod_areas_area_types_list');
            $where = $this->getWhereClause(
                $area_types, 'mod_areas_area_types_list', 'areas.area_type = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        $params = array(
            'select' => array('areas.area_type'),
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
        
        $list = sfConfig::get('mod_areas_area_types_list');
        
        $html  = '<td>' . link_to($result['name'], '@document_by_id?module=areas&id=' . $result['id']) . '</td>';
        $html .= '<td>' . $this->__($list[$result['area_type']]) . '</td>';
        
        return $html;
    }

    public function executeView()
    {
        parent::executeView();
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            // get last geo-associated outings
            $current_doc_id = $this->getRequestParameter('id');
            $latest_outings = array();
            $nb_outings = 0;
            $outing_params = array('areas' => $current_doc_id);
            $nb_outings = sfConfig::get('app_nb_linked_outings_areas');
            $latest_outings = Outing::listLatest($nb_outings + 1, array(), array(), array(), $outing_params, false);
            $latest_outings = Language::getTheBest($latest_outings, 'Outing');
            $this->latest_outings = $latest_outings;
            $this->nb_outings = $nb_outings;
            
            $related_portals = array();
            $id = $this->getRequestParameter('id');
            $areas = array(array('id' => $id));
            Portal::getLocalPortals($related_portals, $areas);
            $this->related_portals = $related_portals;
            
            $area_types_list = sfConfig::get('mod_areas_area_types_list');
            $title = $this->document->get('name') . ' :: ' .
                     $this->__($area_types_list[$this->document->get('area_type')]);
            $this->setPageTitle($title);
        }
    }
    
    public function executeMerge()
    {
        $referer = $this->getRequest()->getReferer();
        $this->setErrorAndRedirect('Areas merging is prohibited', $referer);
    }
    
    public function executeDelete()
    {
        $referer = $this->getRequest()->getReferer();
        $this->setErrorAndRedirect('Areas deletion is prohibited', $referer);
    }
    
    /**
     * filters document creation
     * overrides the one in parent class.
     */
    protected function filterAdditionalParameters()
    {
        $this->setErrorAndRedirect('Area creation is prohibited', '@default_index?module=areas');
    }
    
    public function executeGetmultipleselect()
    {
        $mobile_version = c2cTools::mobileVersion();
        $separate_prefs = $this->hasRequestParameter('sep_prefs') ? $this->getRequestParameter('sep_prefs') : 'true';
        $separate_prefs = ($separate_prefs == 'false') ? false : true;
        $area_type = $this->getRequestParameter('area_type');
        if (!$mobile_version)
        {
            $default_width = 'auto';
            $default_height = '350px';
        }
        else
        {
            $default_width = '216px';
            $default_height = '3.8em';
        }
        $height = $this->hasRequestParameter('height') ? $this->getRequestParameter('height') . 'px' : $default_height;
        $width = $this->hasRequestParameter('width') ? $this->getRequestParameter('width') . 'px' : $default_width;
        $select_id = $this->hasRequestParameter('select_id') ? $this->getRequestParameter('select_id') : 'places';
        $select_name = $this->hasRequestParameter('select_id') ? $this->getRequestParameter('select_name') : 'areas';

        $areas = $this->getAreas($area_type, $separate_prefs);
 
        sfLoader::loadHelpers(array('Tag', 'Form'));
        
        return $this->renderText(select_tag($select_name, 
                        options_for_select($areas, ($separate_prefs ? null : c2cPersonalization::getInstance()->getPlacesFilter())), 
                        array('id' => $select_id, 
                              'multiple' => true,
                              'style' => 'width:'.$width.'; height:'.$height.';'))
                                 .input_hidden_tag($select_id.'_type', $area_type));
    }

    public function executeFilter()
    {
        $this->setPageTitle($this->__('Search a ' . $this->getModuleName()));
        $this->setTemplate('../../documents/templates/filter');
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addNameParam($out, 'anam');
        $this->addParam($out, 'atyp');
        $this->addParam($out, 'acult');

        return $out;
    }
}
