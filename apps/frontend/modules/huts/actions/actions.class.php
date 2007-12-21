<?php
/**
 * huts module actions.
 *
 * @package    c2corg
 * @subpackage huts
 * @version    $Id: actions.class.php 1132 2007-08-01 14:38:06Z fvanderbiest $
 */
class hutsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Hut';

    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, true);
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'hnam': return 'mi.search_name';
            case 'halt': return 'm.elevation';
            case 'styp': return 'm.shelter_type';
            case 'act':  return 'm.activities';
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        if ($areas = $this->getRequestParameter('areas'))
        {
            Document::buildListCondition($conditions, $values, 'ai.id', $areas);
        }

        if ($hname = $this->getRequestParameter('hnam'))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = "%$hname%";
        }

        if ($halt = $this->getRequestParameter('halt'))
        {
            Document::buildCompareCondition($conditions, $values, 'm.elevation', $halt);
        }

        if ($styp = $this->getRequestParameter('styp'))
        {
            $conditions[] = 'm.shelter_type = ?';
            $values[] = $styp;
        }

        if ($activities = $this->getRequestParameter('act'))
        {
            Document::buildActivityCondition($conditions, $values, 'activities', $activities);
        }

        if (!empty($conditions) && !empty($values))
        {
            return array($conditions, $values);
        }

        return array();
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant parameters.
     * @return array
     */
    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'hnam');
        $this->addCompareParam($out, 'halt');
        $this->addListParam($out, 'act');
        $this->addParam($out, 'styp');

        return $out;
    }
}
