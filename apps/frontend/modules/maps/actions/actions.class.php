<?php
/**
 * maps module actions.
 *
 * @package    c2corg
 * @subpackage maps
 * @version    $Id: actions.class.php 2539 2007-12-20 16:58:23Z alex $
 */
class mapsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Map';
    
    /**
     * Nb of dimensions for geom column
     */   
    protected $geom_dims = 2; 

    public function executeMerge()
    {
        $referer = $this->getRequest()->getReferer();
        $this->setErrorAndRedirect('Maps merging is prohibited', $referer);
    }
    
    public function executeDelete()
    {
        $referer = $this->getRequest()->getReferer();
        $this->setErrorAndRedirect('Maps deletion is prohibited', $referer);
    }
    
    /**
     * filters document creation
     * overrides the one in parent class.
     */
    protected function filterAdditionalParameters()
    {
        $this->setErrorAndRedirect('Map creation is prohibited', '@default_index?module=maps');
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'mnam': return 'mi.search_name';
            case 'code': return 'm.code';
            case 'scal': return 'm.scale';
            case 'edit': return 'm.editor';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        $this->buildCondition($conditions, $values, 'Config', '', 'all', 'all');
        if (isset($conditions['all']) && $conditions['all'])
        {
            return array($conditions, $values);
        }
        
        // area criteria
        if ($areas = $this->getRequestParameter('areas'))
        {
            $this->buildCondition($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area');
        }
        elseif ($bbox = $this->getRequestParameter('bbox'))
        {
            Document::buildBboxCondition($conditions, $values, 'm.geom', $bbox);
        }
        
        // map criteria
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('mnam', 'name'));
        $this->buildCondition($conditions, $values, 'Istring', 'm.code', 'code');
        $this->buildCondition($conditions, $values, 'Item', 'm.scale', 'scal');
        $this->buildCondition($conditions, $values, 'Item', 'm.editor', 'edit');
        $this->buildCondition($conditions, $values, 'List', 'm.id', 'id');
        $this->buildCondition($conditions, $values, 'Item', 'mi.culture', 'mcult');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');

        $this->addNameParam($out, 'mnam');
        $this->addParam($out, 'edit');
        $this->addNameParam($out, 'code');
        $this->addParam($out, 'scal');
        $this->addParam($out, 'mcult');

        return $out;
    }
}
