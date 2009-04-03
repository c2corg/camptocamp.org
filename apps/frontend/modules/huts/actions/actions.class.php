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

        $this->buildCondition($conditions, $values, 'List', 'ai.id', 'areas');

        // parking criteria
        $this->buildCondition($conditions, $values, 'String', 'pi.search_name', 'pnam', 'join_parking', true);
        $this->buildCondition($conditions, $values, 'Compare', 'p.elevation', 'palt', 'join_parking');
        $this->buildCondition($conditions, $values, 'List', 'p.public_transportation_rating', 'tp', 'join_parking');
        $this->buildCondition($conditions, $values, 'Array', 'p.public_transportation_types', 'tpty', 'join_parking');

        // hut criteria

        $this->buildCondition($conditions, $values, 'String', 'hi.search_name', array('hnam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'h.elevation', 'halt');
        $this->buildCondition($conditions, $values, 'Bool', 'h.is_staffed', 'hsta');
        $this->buildCondition($conditions, $values, 'List', 'm.shelter_type', 'htyp');
        $this->buildCondition($conditions, $values, 'Array', 'activities', 'act');
        $this->buildCondition($conditions, $values, 'Compare', 'm.staffed_capacity', 'hscap');
        $this->buildCondition($conditions, $values, 'Compare', 'm.unstaffed_capacity', 'hucap');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_matress', 'hmat');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_blanket', 'hbla');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_gas', 'hgas');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_wood', 'hwoo');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');

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
        $this->addListParam($out, 'tpty');

        $this->addNameParam($out, 'hnam');
        $this->addCompareParam($out, 'halt');
        $this->addListParam($out, 'htyp');
        $this->addParam($out, 'hsta');
        $this->addCompareParam($out, 'hscap');
        $this->addCompareParam($out, 'hucap');
        $this->addParam($out, 'hmat');
        $this->addParam($out, 'hbla');
        $this->addParam($out, 'hgas');
        $this->addParam($out, 'hwoo');
        $this->addParam($out, 'geom');
        $this->addListParam($out, 'act');

        return $out;
    }
}
