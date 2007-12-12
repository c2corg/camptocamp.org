<?php
/**
 * maps module actions.
 *
 * @package    c2corg
 * @subpackage maps
 * @version    $Id: actions.class.php 2179 2007-10-25 13:41:55Z alex $
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
    // by default, all documents are 3D (X, Y, Z)
    // exceptions are : 
    //      - users, areas, maps : 2D (X, Y)
    //      - outings : 4D (X, Y, Z, T in traces)
    
    /**
     * Additional fields to display in documents lists (additional, relative to id, culture, name)
     * if field comes from i18n table, prefix with 'mi.', else with 'm.' 
     */  
    protected $fields_in_lists = array('m.code', 'm.scale', 'm.editor');

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
}
