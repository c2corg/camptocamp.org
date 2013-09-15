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
                $associated_parkings = Association::createHierarchyWithBestName($associated_parkings, $prefered_cultures, 'pp');
                $associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
            }
            $this->associated_parkings = $associated_parkings;
            
            $related_portals = array();
            Portal::getLocalPortals($related_portals, $this->associated_areas);
            $this->related_portals = $related_portals;
    
            $product_type_list = sfConfig::get('mod_products_types_list');
            $product_type_index_list = $this->document->get('product_type');
            $product_type_name_list = array();
            foreach($product_type_index_list as $product_type_index)
            {
                $product_type_name_list[] = $this->__($product_type_list[$product_type_index]);
            }
            $product_types = implode(', ', $product_type_name_list);
            $doc_name = $this->document->get('name');
            
            $title = $doc_name;
            if ($this->document->isArchive())
            {
                $version = $this->getRequestParameter('version');
                $title .= ' :: ' . $this->__('revision') . ' ' . $version ;
            }
            $title .= ' :: ' . $product_types;
            $this->setPageTitle($title);

            $description = array($product_types . ' :: ' . $doc_name, $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant parameters.
     * @return array
     */
    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addAroundParam($out, 'farnd');
        
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
    // FIXME : Doctrine bug - see ticket #687
    //    $this->addListParam($out, 'tpty');

        $this->addNameParam($out, 'fnam');
        $this->addCompareParam($out, 'falt');
        $this->addListParam($out, 'ftyp');
        $this->addParam($out, 'geom');
        $this->addParam($out, 'fcult');

        return $out;
    }

    /**
     * Executes list action
     */
    public function executeList()
    {
        parent::executeList();

        $nb_results = $this->nb_results;
        if ($nb_results == 0) return;

        $timer = new sfTimer();
        $products = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());
        
        $timer = new sfTimer();
        Parking::addAssociatedParkings($products, 'pf'); // add associated parkings infos to $products
        c2cActions::statsdTiming('parking.addAssociatedParkings', $timer->getElapsedTime());

        Area::sortAssociatedAreas($products);

        $this->items = Language::parseListItems($products, 'Product');
    }
}
