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
        $this->associated_summits = array_filter($this->associated_docs, array('c2cTools', 'is_summit')); 
        $this->associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route')); 
        $this->associated_huts = array_filter($this->associated_docs, array('c2cTools', 'is_hut')); 
        $this->associated_sites = array_filter($this->associated_docs, array('c2cTools', 'is_site')); 
    }

    public function executeFilter()
    {   
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

        if ($bname = $this->getRequestParameter('bnam'))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = "%$bname%";
        }

        if ($auth = $this->getRequestParameter('auth'))
        {
            $conditions[] = 'm.author ILIKE ?';
            $values[] = "%$auth%";
        }

        if ($edit = $this->getRequestParameter('edit'))
        {
            $conditions[] = 'm.edit ILIKE ?';
            $values[] = "%$edit%";
        }

        if ($btyp = $this->getRequestParameter('btyp'))
        {
            $conditions[] = '? = ANY (book_types)';
            $values[] = $btyp;
        }

        if ($activities = $this->getRequestParameter('act'))
        {
            Document::buildActivityCondition($conditions, $values, 'activities', $activities);
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

        $this->addNameParam($out, 'bnam');
        $this->addNameParam($out, 'auth');
        $this->addNameParam($out, 'edit');
        $this->addListParam($out, 'act');
        $this->addParam($out, 'btyp');

        return $out;
    }
}
