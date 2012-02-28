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

            $associated_summits = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_summit')), 'name');
            $summit_ids = $summit_routes_tmp = $summit_routes_ids = array();
            if (count($associated_summits))
            {
                foreach ($associated_summits as $summit) // there should be only one...
                {
                    $summit_ids[] = $summit['id'];
                }
                $summit_routes_tmp = Association::countLinked($summit_ids, 'sr');
                foreach ($summit_routes_tmp as $route)
                {
                    $summit_routes_ids[] = $route['linked_id'];
                }
            }

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
            
            $associated_summit_routes = array();
            if (count($summit_routes_ids))
            {
                foreach ($associated_routes as $key => $route)
                {
                    if (in_array($route['id'], $summit_routes_ids))
                    {
                        $associated_summit_routes[$key] = $route;
                        unset($associated_routes[$key]);
                    }
                }
            }
            
            $this->associated_routes = $associated_routes;
            $this->associated_summit_routes = $associated_summit_routes;
            
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
            $this->section_list = array('books' => ($cab != 0), 'map' => (boolean)($this->document->get('geom_wkt')));
    
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

    protected function endEdit()
    {
// TODO when a new lang version is created!!!
// TODO geoasociation here and on creation !!
        if ($this->success) // form submitted and success (doc has been saved) // TODO check if this is ok
        {
            // before redirecting view, we check if either the name, elevation or geolocalization of the hut
            // has changed and pass on those changes to the 'ghost summit'
            $hut_doc = $this->document;

            $summit_doc = $hut_doc->getGhostSummit();

            // check wether elevation, name or geometry of the hut has changed, and
            // change accordingly the ghost summit if that's the case
            if ($summit_doc != false)
            {
                if ($hut_doc->get('elevation') !== $summit_doc->get('elevation') ||
                    $hut_doc->get('lat') !== $summit_doc->get('lat') ||
                    $hut_doc->get('lon') !== $summit_doc->get('lon') ||
                    $hut_doc->get('name') !== $summit_doc->get('name'))
                {
                    $conn = sfDoctrine::Connection();
                    try
                    {
                        $conn->beginTransaction();
                        $history_metadata = new HistoryMetadata();
                        $history_metadata->set('is_minor', false);// TODO get from parameter
                        $history_metadata->set('user_id', 2); // C2C user // TODO get user
                        $history_metadata->setComment('plop'); // TODO get comment or dedicated one?
                        $history_metadata->save();

                        $summit_doc->set('name', $hut_doc->get('name'));
                        $summit_doc->set('lon', $hut_doc->get('lon'));
                        $summit_doc->set('lat', $hut_doc->get('lat'));
                        $summit_doc->set('elevation', $hut_doc->get('elevation'));
                        $summit_doc->save();

                        $conn->commit();
                        $this->clearCache('summits', $summit_doc->get('id'));
                    }
                    catch (Exception $e)
                    {
                        $conn->rollback();
                        return $this->setErrorAndRedirect("Failed to synchronize summit", 'routes/edit?link=' . $summit_doc->get('id'));
                    }
                }
            }
            parent::endEdit(); // redirect to document view
        }
    }

    public function executeAddroute()
    {
      $id = $this->getRequestParameter('document_id');
      
      // check if a summit is already associated to hut. if not, create it
      $create_summit = (Association::countMains($id, 'sh') == 0);
      
      if ($create_summit)
      {
          $document = Document::find('Hut', $id, array('elevation', 'geom_wkt'));
          $conn = sfDoctrine::Connection();
          try
          {
              $conn->beginTransaction();
  
              // create first version of document, with culture and geometry of hut document
              $hut_elevation = $document['elevation'];
              $hut_geom = $document['geom_wkt'];
              $hut_culture = $document->getCulture();
              $hut_name = $document['name'];
  
              $history_metadata = new HistoryMetadata();
              $history_metadata->setComment('Created summit synchronized with hut for access');
              $history_metadata->set('is_minor', false);
              $history_metadata->set('user_id', 2); // C2C user
              $history_metadata->save();
  
              $summit = new Summit();
              $summit->setCulture($hut_culture);
              $summit->set('name', $hut_name);
              $summit->set('elevation', $hut_elevation);
  
              $summit->set('geom_wkt', $hut_geom);
              $summit->save();

              $conn->commit();

              // add others culture versions
              foreach ($document->get('HutI18n') as $i18n)
              {
                  $culture = $i18n->getCulture();
                  if ($culture != $hut_culture)
                  {
                      $conn->beginTransaction();
                      $hut_name = $i18n->getName();

                      $history_metadata = new HistoryMetadata();
                      $history_metadata->setComment('Created summit synchronized with hut for access');
                      $history_metadata->set('is_minor', false);
                      $history_metadata->set('user_id', 2); // C2C user
                      $history_metadata->save();

                      $summit->setCulture($culture);
                      $summit->set('name', $hut_name);
                      $summit->save();
                      $conn->commit();
                  }
              }
          }
          catch (Exception $e)
          {
              $conn->rollback();
              return $this->setErrorAndRedirect("Failed to create synchronized summit", "routes/edit?link=$summit_id");
          }
          
          
          $summit_id = $summit->get('id');
          
          
          // associate hut to summit
          $asso = new Association();
          $asso->doSaveWithValues($summit_id, $id, 'sh', 2); // C2C user
      }
      else
      {
        $associations = Association::findAllAssociations($id, 'sh');
        $summit_id = $associations[0]->get('main_id');
      }
      $this->clearCache('huts', $id);
      return $this->redirect("routes/edit?link=$summit_id");
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
            case 'anam': return 'ai.search_name';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {
        $params_list = c2cTools::getAllRequestParameters();
        
        return Hut::buildListCriteria($params_list);
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant parameters.
     * @return array
     */
    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');
        $this->addAroundParam($out, 'harnd');
        
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
        $this->addParam($out, 'hcult');

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
        
        $huts = $this->pager->getResults('array');

        Parking::addAssociatedParkings($huts, 'ph'); // add associated parkings infos to $huts
        Document::countAssociatedDocuments($huts, 'hr', true);
        $this->items = Language::parseListItems($huts, 'Hut');
    }
}
