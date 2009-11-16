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
        
        if (!$this->document->isArchive() && $this->document['redirects_to'] == NULL)
        {
            $user = $this->getUser();
            $prefered_cultures = $user->getCulturesForDocuments();
            $current_doc_id = $this->getRequestParameter('id');
            
            $main_associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            
            $parking_ids = array();
            if (count($main_associated_parkings))
            {
                $associated_parkings = Association::addChildWithBestName($main_associated_parkings, $prefered_cultures, 'pp', $current_doc_id);
                $associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
                
                if (count($main_associated_parkings) > 1 || count($associated_parkings) == 1)
                {
                    foreach ($main_associated_parkings as $parking)
                    {
                        $parking_ids[] = $parking['id'];
                    }
                }
                
                if (count($parking_ids))
                {
                    $associated_parking_routes = Association::findWithBestName($parking_ids, $prefered_cultures, 'pr');
                    $this->associated_docs = array_merge($this->associated_docs, $associated_parking_routes);
                }
            }
            else
            {
                $associated_parkings = $main_associated_parkings;
            }
            
            $this->associated_parkings = $associated_parkings;
            
            array_unshift($parking_ids, $current_doc_id);
            $this->ids = implode('-', $parking_ids);
            
            $associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__('&nbsp;:').' ');
            $this->associated_routes = $associated_routes;
            
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
                    $associated_route_docs = Association::findWithBestName($route_ids, $prefered_cultures, array('br'), false, false);
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
            
            if (count($associated_routes_books))
            {
                $associated_books = Book::getAssociatedBooksData($associated_routes_books);
            }
            $this->associated_books = $associated_books;
            
            $this->associated_huts = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_hut')), 'elevation');
            
            $cab = count($associated_books);
            $this->section_list = array('books' => ($cab != 0));
    
            $description = array($this->__('parking') . ' :: ' . $this->document->get('name'),
                                 $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePopup()
    {
        parent::executePopup();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__('&nbsp;:').' ', $this->document->get('id'));
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'pnam': return 'mi.name';
            case 'palt': return 'm.elevation';
            case 'tp':  return 'm.public_transportation_rating';
            case 'tpty':  return 'm.public_transportation_types';
            case 'scle':  return 'm.snow_clearance_rating';
            case 'anam': return 'ai.name';
            case 'geom': return 'm.geom_wkt';
            default: return NULL;
        }
    } 

    protected function getListCriteria()
    {   
        $conditions = $values = array();

        $this->buildCondition($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area');
        $this->buildCondition($conditions, $values, 'String', 'mi.search_name', array('pnam', 'name'));
        $this->buildCondition($conditions, $values, 'Compare', 'm.elevation', 'palt');
        $this->buildCondition($conditions, $values, 'List', 'm.public_transportation_rating', 'tp');
        $this->buildCondition($conditions, $values, 'Array', 'p.public_transportation_types', 'tpty');
        $this->buildCondition($conditions, $values, 'Georef', null, 'geom');
        $this->buildCondition($conditions, $values, 'List', 'm.id', 'id');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public function executeFilter()
    {
        parent::executeFilter();
    }

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');
        $this->addParam($out, 'geom');
        
        return $out;
    }
}
