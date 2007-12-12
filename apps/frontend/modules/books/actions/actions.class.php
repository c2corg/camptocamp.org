<?php
/**
 * books module actions.
 *
 * @package    c2corg
 * @subpackage books
 * @version    $Id: actions.class.php 2261 2007-11-03 15:05:40Z alex $
 */
class booksActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Book';
 
    
    /**
     * Additional fields to display in documents lists (additional, relative to id, culture, name)
     * if field comes from i18n table, prefix with 'mi.', else with 'm.' 
     */  
    protected $fields_in_lists = array('m.author', 'm.activities', 'm.editor', 'm.book_types');

    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        $this->associated_summits = array_filter($this->associated_docs, array('c2cTools', 'is_summit')); 
        $this->associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route')); 
        $this->associated_huts = array_filter($this->associated_docs, array('c2cTools', 'is_hut')); 
        $this->associated_sites = array_filter($this->associated_docs, array('c2cTools', 'is_site')); 
    }
}
