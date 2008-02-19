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

    public function executeView()
    {
        parent::executeView();
    }
    
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

    public function executeFilter()
    {
        $this->setTemplate('../../documents/templates/filter');
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

        if ($mname = $this->getRequestParameter('mnam', $this->getRequestParameter('name')))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = '%' . urldecode($mname) . '%';
        }

        if ($code = $this->getRequestParameter('code'))
        {
            $conditions[] = 'm.code ILIKE ?';
            $values[] = '%' . urldecode($code) . '%';
        }

        if ($scal = $this->getRequestParameter('scal'))
        {
            $conditions[] = 'm.scale = ?';
            $values[] = $scal;
        }

        if ($edit = $this->getRequestParameter('edit'))
        {
            $conditions[] = 'm.editor = ?';
            $values[] = $edit;
        }

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addNameParam($out, 'mnam');
        $this->addNameParam($out, 'code');
        $this->addParam($out, 'scal');
        $this->addParam($out, 'edit');

        return $out;
    }
}
