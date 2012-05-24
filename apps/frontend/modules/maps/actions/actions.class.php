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
        // add editor + code for in map title
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $related_portals = array();
            Portal::getLocalPortals($related_portals, $this->associated_areas);
            $this->related_portals = $related_portals;
            
            $map_editors_list = sfConfig::get('mod_maps_editors_list');
            $title = $this->__($map_editors_list[$this->document->get('editor')]) . ' ' .
                     $this->document->get('code') . ' ' . $this->document->get('name') .
                     ' :: ' . $this->__(substr($this->getModuleName(), 0, -1));
            $this->setPageTitle($title);
        }
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
        $params_list = c2cTools::getAllRequestParameters();
        
        return Map::buildListCriteria($params_list);
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
