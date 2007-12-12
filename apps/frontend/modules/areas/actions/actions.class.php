<?php
/**
 * areas module actions.
 *
 * @package    c2corg
 * @subpackage areas
 * @version    $Id: actions.class.php 2091 2007-10-18 14:30:42Z elemoine $
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
    // by default, all documents are 3D (X, Y, Z)
    // exceptions are : 
    //      - users and areas : 2D (X, Y)
    //      - outings : 4D (X, Y, Z, T in traces)
    
    /**
     * Additional fields to display in documents lists (additional, relative to id, culture, name)
     * if field comes from i18n table, prefix with 'mi.', else with 'm.' 
     */  
    protected $fields_in_lists = array('m.area_type');

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
    
}
