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
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs);
        $this->associated_huts = Hut::getAssociatedHutsData($this->associated_docs);
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'pnam': return 'mi.name';
            case 'palt': return 'm.elevation';
            case 'tp':  return 'm.public_transportation_rating';
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

        if ($pname = $this->getRequestParameter('pnam', $this->getRequestParameter('name')))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = '%' . urldecode($pname) . '%';
        }

        if ($palt = $this->getRequestParameter('palt'))
        {
            Document::buildCompareCondition($conditions, $values, 'm.elevation', $palt);
        }

        if ($tp = $this->getRequestParameter('tp'))
        {
            $conditions[] = 'm.public_transportation_rating = 1';
        }

        if ($geom = $this->getRequestParameter('geom'))
        {
            Document::buildGeorefCondition($conditions, $geom);
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

        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addParam($out, 'tp');
        $this->addParam($out, 'geom');
        
        return $out;
    }
}
