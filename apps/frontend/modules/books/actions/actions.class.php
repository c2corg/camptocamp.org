<?php
/**
 * books module actions.
 *
 * @package    c2corg
 * @subpackage books
 * @version    $Id: actions.class.php 2539 2007-12-20 16:58:23Z alex $
 */
class booksActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Book';
 
    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();

        if (!$this->document->isArchive())
        {
            $this->associated_summits = Summit::getAssociatedSummitsData($this->associated_docs);
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_huts = Hut::getAssociatedHutsData($this->associated_docs);
            $this->associated_sites = Site::getAssociatedSitesData($this->associated_docs);
    
            $cas = count($this->associated_summits);
            $car = count($this->associated_routes);
            $cah = count($this->associated_huts);
            $cab = count($this->associated_sites);
    
            $this->section_list = array('summits' => ($cas != 0),
                                        'routes' => ($car != 0),
                                        'huts' => ($cah != 0),
                                        'sites' => ($cab != 0),
                                        'docs' => ($cas + $car + $cah +$cab == 0));
    
            $description = array($this->__('book') . ' :: ' . $this->document->get('name'),
                                 $this->getActivitiesList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executeFilter()
    {   
        $this->setPageTitle($this->__('Search a ' . $this->getModuleName()));
        $this->setTemplate('../../documents/templates/filter');
    } 

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'bnam': return 'mi.search_name';
            case 'act':  return 'm.activities';
            case 'auth': return 'm.author';
            case 'edit': return 'm.editor';
            case 'btyp': return 'm.book_type';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('bnam', 'name'));
        $this->buildCondition($conditions, $values, 'Istring', 'm.author', 'auth');
        $this->buildCondition($conditions, $values, 'Istring', 'm.editor', 'edit');
        $this->buildCondition($conditions, $values, 'Array', 'book_types', 'btyp');
        $this->buildCondition($conditions, $values, 'Array', 'activities', 'act');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addNameParam($out, 'bnam');
        $this->addNameParam($out, 'auth');
        $this->addNameParam($out, 'edit');
        $this->addListParam($out, 'act');
        $this->addListParam($out, 'btyp');

        return $out;
    }
}
