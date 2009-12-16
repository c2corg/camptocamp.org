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

            $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));
            
            if ($this->document->get('shelter_type') == 5)
            {
                $parking_ids = array();
                foreach ($associated_parkings as $parking)
                {
                    $parking_ids[] = $parking['id'];
                }
                
                $route_ids = array();
                foreach ($associated_routes as $route)
                {
                    $route_ids[] = $route['id'];
                }
                
                $associated_parking_routes = Association::findWithBestName($parking_ids, $prefered_cultures, 'pr', false, true, $route_ids);
                $associated_routes = array_merge($associated_routes, $associated_parking_routes);
            }
            
            $associated_routes = Route::getAssociatedRoutesData($associated_routes, $this->__(' :').' ');
            $this->associated_routes = $associated_routes;
            
            $associated_books = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_book')));
            
            $route_ids = array();
            $associated_routes_books = array();
            if (count($associated_routes))
            {
                foreach ($associated_routes as $route)
                {
                    if ($route['duration'] instanceof Doctrine_Null || $route['duration'] <= 4)
                    {
                        $route_ids[] = $route['id'];
                    }
                }
                
                if (count($route_ids))
                {
                    $book_ids = array();
                    foreach ($associated_books as $book)
                    {
                        $book_ids[] = $book['id'];
                    }
                    $associated_route_docs = Association::findWithBestName($route_ids, $prefered_cultures, array('br'), false, false, $book_ids);
                    if (count($associated_route_docs))
                    {
                        $associated_route_docs = c2cTools::sortArray($associated_route_docs, 'name');
                        $associated_routes_books = array_filter($associated_route_docs, array('c2cTools', 'is_book'));
                        foreach ($associated_routes_books as $key => $book)
                        {
                            $associated_routes_books[$key]['parent_id'] = true;
                        }
                    }
                }
            }
            
            $associated_books = array_merge($associated_books, $associated_routes_books);
            if (count($associated_books))
            {
                $associated_books = Book::getAssociatedBooksData($associated_books);
            }
            $this->associated_books = $associated_books;
            
            $cab = count($associated_books);
            $this->section_list = array('books' => ($cab != 0));
    
            $hut_type_list = sfConfig::get('mod_huts_shelter_types_list');
            $hut_type_index = $this->document->get('shelter_type');
            $hut_type = $this->__($hut_type_list[$hut_type_index]);
            $doc_name = $this->document->get('name');
            
            $title = $doc_name;
            if ($this->document->isArchive())
            {
                $version = $this->getRequestParameter('version');
                $title .= ' :: ' . $this->__('revision') . ' ' . $version ;
            }
            $title .= ' :: ' . $hut_type;
            $this->setPageTitle($title);

            $description = array($hut_type . ' :: ' . $doc_name,
                                 $this->getActivitiesList(), $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePopup()
    {
        parent::executePopup();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'hnam': return 'mi.search_name';
            case 'halt': return 'm.elevation';
            case 'styp': return 'm.shelter_type';
            case 'hscap': return 'm.staffed_capacity';
            case 'hucap': return 'm.unstaffed_capacity';
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

        // hut criteria

        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('hnam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'halt');
        $this->buildCondition($conditions, $values, 'Bool', 'm.is_staffed', 'hsta');
        $this->buildCondition($conditions, $values, 'List', 'm.shelter_type', 'htyp');
        $this->buildCondition($conditions, $values, 'Array', 'h.activities', 'act');
        $this->buildCondition($conditions, $values, 'Compare', 'm.staffed_capacity', 'hscap');
        $this->buildCondition($conditions, $values, 'Compare', 'm.unstaffed_capacity', 'hucap');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_matress', 'hmat');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_blanket', 'hbla');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_gas', 'hgas');
        $this->buildCondition($conditions, $values, 'Bool', 'm.has_unstaffed_wood', 'hwoo');
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
        $this->addListParam($out, 'act');
        $this->addParam($out, 'geom');

        return $out;
    }

    /**
     * Executes list action
     */
    public function executeList()
    {
        parent::executeList();

        $huts = $this->pager->getResults('array');

        if (count($huts) == 0) return;
        
        Parking::addAssociatedParkings($huts, 'ph'); // add associated parkings infos to $huts
        Document::countAssociatedDocuments($huts, 'hr', true);
        $this->items = Language::parseListItems($huts, 'Hut');
    }
}
