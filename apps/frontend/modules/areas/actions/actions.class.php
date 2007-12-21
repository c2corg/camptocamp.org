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
        $area_id = $this->getRequestParameter('area_type');
        $areas = $this->getAreas($area_id);
        
        sfLoader::loadHelpers(array('Tag', 'Form'));
        
        return $this->renderText(select_tag('areas', 
                        options_for_select($areas), 
                        array('id' => 'areas', 
                              'multiple' => true,
                              'style' => 'width:300px; height:100px;')));
    }

    public function executeFilter()
    {
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

        if ($aname = $this->getRequestParameter('anam'))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = "%$sname%";
        }

        if ($atyp = $this->getRequestParameter('atyp'))
        {
            $conditions[] = 'm.area_type = ?';
            $values[] = $atyp;
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
            case 'anam': return 'mi.search_name';
            case 'atyp': return 'm.area_type';
            default: return NULL;
        }
    }
}
