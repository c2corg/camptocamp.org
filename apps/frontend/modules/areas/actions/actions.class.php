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
        $separate_prefs = $this->hasRequestParameter('sep_prefs') ? $this->getRequestParameter('sep_prefs') : 'true';
        $separate_prefs = ($separate_prefs == 'false') ? false : true;
        $area_type = $this->getRequestParameter('area_type');
        $height = $this->hasRequestParameter('height') ? $this->getRequestParameter('height') : 100;
        $width = $this->hasRequestParameter('width') ? $this->getRequestParameter('width') : 400;
        $select_id = $this->hasRequestParameter('select_id') ? $this->getRequestParameter('select_id') : 'places';
        $select_name = $this->hasRequestParameter('select_id') ? $this->getRequestParameter('select_name') : 'areas';

        $areas = $this->getAreas($area_type, $separate_prefs);
 
        sfLoader::loadHelpers(array('Tag', 'Form'));
        
        return $this->renderText(select_tag($select_name, 
                        options_for_select($areas, ($separate_prefs ? null : c2cPersonalization::getInstance()->getPlacesFilter())), 
                        array('id' => $select_id, 
                              'multiple' => true,
                              'style' => 'width:'.$width.'px; height:'.$height.'px;'))
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

        return $out;
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('anam', 'name'));
        $this->buildCondition($conditions, $values, 'Item', 'm.area_type', 'atyp');

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
            case 'anam': return 'mi.search_name';
            case 'atyp': return 'm.area_type';
            default: return NULL;
        }
    }
}
