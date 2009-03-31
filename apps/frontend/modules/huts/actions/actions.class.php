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
        
        if (!$this->document->isArchive())
        {
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs);

            $this->associated_parkings = array_filter($this->associated_docs, array('c2cTools', 'is_parking'));

            $description = array($this->__('hut') . ' :: ' . $this->document->get('name'),
                                 $this->getActivitiesList(), $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePopup()
    {
        parent::executePopup();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs);
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

        // parking criteria

        if ($pname = $this->getRequestParameter('pnam'))
        {
            $conditions[] = 'pi.search_name LIKE remove_accents(?)';
            $values[] = '%' . urldecode($pname) . '%';
            $conditions['join_parking'] = true;
            $conditions['join_parking_i18n'] = true;
        }

        if ($palt = $this->getRequestParameter('palt'))
        {
            Document::buildCompareCondition($conditions, $values, 'p.elevation', $palt);
            $conditions['join_parking'] = true;
        }

        if ($tp = $this->getRequestParameter('tp'))
        {
            Document::buildListCondition($conditions, $values, 'p.public_transportation_rating', $tp);
            $conditions['join_parking'] = true;
        }

        // hut criteria

        if ($hname = $this->getRequestParameter('hnam', $this->getRequestParameter('name')))
        {
            $conditions[] = 'mi.search_name LIKE remove_accents(?)';
            $values[] = '%' . urldecode($hname) . '%';
        }

        if ($halt = $this->getRequestParameter('halt'))
        {
            Document::buildCompareCondition($conditions, $values, 'm.elevation', $halt);
        }

        if ($ista = $this->getRequestParameter('ista'))
        {
            Document::buildBoolCondition($conditions, 'm.is_staffed', $ista);
        }

        if ($styp = $this->getRequestParameter('styp'))
        {
            Document::buildListCondition($conditions, $values, 'm.shelter_type', $styp);
        }

        if ($activities = $this->getRequestParameter('act'))
        {
            Document::buildArrayCondition($conditions, $values, 'activities', $activities);
        }

        if ($scap = $this->getRequestParameter('scap'))
        {
            Document::buildCompareCondition($conditions, $values, 'm.staffed_capacity', $scap);
        }

        if ($ucap = $this->getRequestParameter('ucap'))
        {
            Document::buildCompareCondition($conditions, $values, 'm.unstaffed_capacity', $ucap);
        }

        if ($hmat = $this->getRequestParameter('hmat'))
        {
            Document::buildBoolCondition($conditions, 'm.has_unstaffed_matress', $hmat);
        }

        if ($hbla = $this->getRequestParameter('hbla'))
        {
            Document::buildBoolCondition($conditions, 'm.has_unstaffed_blanket', $hbla);
        }

        if ($hgas = $this->getRequestParameter('hgas'))
        {
            Document::buildBoolCondition($conditions, 'm.has_unstaffed_gas', $hgas);
        }

        if ($hwoo = $this->getRequestParameter('hwoo'))
        {
            Document::buildBoolCondition($conditions, 'm.has_unstaffed_wood', $hwoo);
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

    /**
     * Parses REQUEST sent by filter form and keeps only relevant parameters.
     * @return array
     */
    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');

        $this->addNameParam($out, 'hnam');
        $this->addCompareParam($out, 'halt');
        $this->addListParam($out, 'act');
        $this->addListParam($out, 'styp');
        $this->addParam($out, 'ista');
        $this->addCompareParam($out, 'scap');
        $this->addCompareParam($out, 'ucap');
        $this->addParam($out, 'hmat');
        $this->addParam($out, 'hbla');
        $this->addParam($out, 'hgas');
        $this->addParam($out, 'hwoo');
        $this->addParam($out, 'geom');

        return $out;
    }
}
