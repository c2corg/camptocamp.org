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
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, true);
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

        if ($pname = $this->getRequestParameter('pnam'))
        {
            $conditions[] = 'pi.search_name LIKE remove_accents(?)';
            $values[] = "%$pname%";
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
            $conditions[] = 'p.public_transportation_rating = 1';
            $conditions['join_parking'] = true;
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
        
        return $out;
    }
}
