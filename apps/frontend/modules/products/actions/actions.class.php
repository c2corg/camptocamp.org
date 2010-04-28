<?php
/**
 * products module actions.
 *
 * @package    c2corg
 * @subpackage products
  */
class productsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Product';

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
            
            $associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            if (count($associated_parkings))
            {
                $associated_parkings = Association::addChildWithBestName($associated_parkings, $prefered_cultures, 'pp');
                $associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
            }
            $this->associated_parkings = $associated_parkings;
    
            $product_type_list = sfConfig::get('mod_products_types_list');
            $product_type_index = $this->document->get('product_type');
            $product_type = $this->__($product_type_list[$product_type_index]);
            $doc_name = $this->document->get('name');
            
            $title = $doc_name;
            if ($this->document->isArchive())
            {
                $version = $this->getRequestParameter('version');
                $title .= ' :: ' . $this->__('revision') . ' ' . $version ;
            }
            $title .= ' :: ' . $product_type;
            $this->setPageTitle($title);

            $description = array($product_type . ' :: ' . $doc_name, $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'fnam': return 'mi.search_name';
            case 'falt': return 'm.elevation';
            case 'ftyp': return 'm.product_type';
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

        // parking criteria
        $this->buildCondition($conditions, $values, 'String', 'pi.search_name', 'pnam', 'join_parking', true);
        $this->buildCondition($conditions, $values, 'Compare', 'p.elevation', 'palt', 'join_parking');
        $this->buildCondition($conditions, $values, 'List', 'p.public_transportation_rating', 'tp', 'join_parking');
        $this->buildCondition($conditions, $values, 'Array', 'p.public_transportation_types', 'tpty', 'join_parking');
        $this->buildCondition($conditions, $values, 'List', 'l.main_id', 'parking', 'join_parking_id');

        // product criteria
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('fnam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'falt');
        $this->buildCondition($conditions, $values, 'Array', 'f.product_type', 'ftyp');
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
        
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');

        $this->addNameParam($out, 'fnam');
        $this->addCompareParam($out, 'falt');
        $this->addListParam($out, 'ftyp');
        $this->addParam($out, 'geom');

        return $out;
    }

    /**
     * Executes list action
     */
    public function executeList()
    {
        parent::executeList();

        $products = $this->pager->getResults('array');

        if (count($products) == 0) return;
        
        Parking::addAssociatedParkings($products, 'pf'); // add associated parkings infos to $products
        $this->items = Language::parseListItems($products, 'Product');
    }
}
