<?php
/**
 * parkings module actions.
 *
 * @package    c2corg
 * @subpackage parkings
 * @version    $Id: actions.class.php 1132 2007-08-01 14:38:06Z fvanderbiest $
 */
class parkingsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Parking';

    
    /**
     * Additional fields to display in documents lists (additional, relative to id, culture, name)
     * if field comes from i18n table, prefix with 'mi.', else with 'm.' 
     */  
    protected $fields_in_lists = array('m.elevation', 'm.public_transportation_rating');
 
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, true);
    }
}
