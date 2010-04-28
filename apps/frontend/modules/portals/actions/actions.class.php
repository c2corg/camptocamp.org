<?php
/**
 * portals module actions.
 *
 * @package    c2corg
 * @subpackage portals
 */
class portalsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Portal';

    /**
     * Executes view action.
     */
    public function executeView()
    {
        parent::executeView();
        
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();
            
        }
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'wnam': return 'mi.search_name';
            case 'walt': return 'm.elevation';
            case 'act':  return 'm.activities';
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        $this->buildCondition($conditions, $values, 'Config', '', 'all', 'all');
        if (isset($conditions['all']) && $conditions['all'])
        {
            return array($conditions, $values);
        }
        
        // area criteria
        if ($areas = $this->getRequestParameter('areas'))
        {
            $this->buildCondition($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area');
        }
        elseif ($bbox = $this->getRequestParameter('bbox'))
        {
            Document::buildBboxCondition($conditions, $values, 'm.geom', $bbox);
        }
        
        // portal criteria

        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('wnam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'walt');
        $this->buildCondition($conditions, $values, 'Array', 'w.activities', 'act');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');
        $this->buildCondition($conditions, $values, 'List', 'm.id', 'id');

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
        
        $this->addNameParam($out, 'wnam');
        $this->addCompareParam($out, 'walt');
        $this->addListParam($out, 'act');
        $this->addParam($out, 'geom');

        return $out;
    }
}
