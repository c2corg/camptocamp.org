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
            $current_doc_id = $this->getRequestParameter('id');
            $is_gite = ($this->document->get('shelter_type') == 5);

            // retrieve 2-hops parkings
            $associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            if (count($associated_parkings))
            {
                $associated_parkings = Association::createHierarchyWithBestName($associated_parkings, $prefered_cultures,
                    array('type' => 'pp'));
                $associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
            }
            $this->associated_parkings = $associated_parkings;

            $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));

            // associated summits
            $associated_summits = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_summit')), 'name');
            $this->associated_summits = $associated_summits;

            $summit_ids = $summit_routes_tmp = $summit_routes_ids = array();
            if (count($associated_summits))
            {
                foreach ($associated_summits as $summit) // there should be only one...
                {
                    $summit_ids[] = $summit['id'];
                }
                $summit_routes_tmp = Association::countAllLinked($summit_ids, 'sr');
                foreach ($summit_routes_tmp as $route)
                {
                    $summit_routes_ids[] = $route['linked_id'];
                }
            }

            // for a gite, routes  and sites are not directly linked. We retrieve the routes linked to the linked parkings
            if ($is_gite)
            {
                $parking_ids = array();
                foreach ($associated_parkings as $parking)
                {
                    $parking_ids[] = $parking['id'];
                }

                $associated_parking_docs = Association::findLinkedDocsWithBestName($parking_ids, $prefered_cultures, array('pr', 'pt'), false, true);

                $associated_routes = array_filter($associated_parking_docs, array('c2cTools', 'is_route'));

                $associated_parking_sites = c2cTools::sortArrayByName(array_filter($associated_parking_docs, array('c2cTools', 'is_site')));
                $this->associated_sites = array_merge($this->associated_sites,$associated_parking_sites); // associated sites should be empty!! Else it violates moderation rules
                $this->ids = implode('-', $parking_ids);
            }
            else
            {
                $associated_routes = array_filter($this->associated_docs, array('c2cTools', 'is_route'));
                $this->associated_sites = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_site')));
                $this->ids = $current_doc_id;
            }

            // get additional data for routes
            $associated_routes = Route::getAssociatedRoutesData($associated_routes, $this->__(' :').' ', reset($summit_ids));

            // these are the routes where the hut act as a summit
            // they are displayed in a specific section
            $summit_ids = $summit_routes_tmp = $summit_routes_ids = $associated_summit_routes = array();
            if (count($associated_summits)) // we have one "ghost summit"
            {
                foreach ($associated_summits as $summit) // there should be only one...
                {
                    $summit_ids[] = $summit['id'];
                }
                $summit_routes_tmp = Association::countAllLinked($summit_ids, 'sr');
                foreach ($summit_routes_tmp as $route)
                {
                    $summit_routes_ids[] = $route['linked_id'];
                }
            }
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

            // We retrieve both the books directly linked
            $associated_books = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_book')));

            // AND the books linked to linked routes
            // FIXME we should probably also do this with linked sites
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
                    $associated_route_docs = Association::findLinkedDocsWithBestName($route_ids, $prefered_cultures, array('br'), false, false, $book_ids);
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

            // get associated outings
            $latest_outings = array();
            $nb_outings = 0;
            if (!$is_gite && count($associated_routes) || $is_gite && count($parking_ids))
            {
                if (!$is_gite)
                {
                    $outing_params = array('huts' => $current_doc_id);
                }
                else
                {
                    $outing_params = array('parkings' => $this->ids);
                }
                $nb_outings = sfConfig::get('app_nb_linked_outings_docs');
                $latest_outings = Outing::listLatest($nb_outings + 1, array(), array(), array(), $outing_params, false);
                $latest_outings = Language::getTheBest($latest_outings, 'Outing');
            }
            $this->latest_outings = $latest_outings;
            $this->nb_outings = $nb_outings;
            
            // possibly related portals
            $related_portals = array();
            Portal::getRelatedPortals($related_portals, $this->associated_areas, $associated_routes);
            $this->related_portals = $related_portals;
            
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
        if ($this->success) // form submitted and success (doc has been saved)
        {
            // before redirecting view, we check if either the name, elevation or geolocalization of the hut
            // has changed and pass on those changes to the 'ghost summit'
            $hut_doc = $this->document;

            $summit_doc = $hut_doc->getGhostSummit();

            // check wether elevation, name or geometry of the hut has changed, and
            // change accordingly the ghost summit if that's the case
            if ($summit_doc != false)
            {
                $geom_changed = $hut_doc->get('lat') !== $summit_doc->get('lat') ||
                                $hut_doc->get('lon') !== $summit_doc->get('lon');
                if ($hut_doc->get('elevation') !== $summit_doc->get('elevation') ||
                    $hut_doc->get('name') !== $summit_doc->get('name') ||
                    $geom_changed)
                {
                    c2cTools::log('Updating ghost summit of hut');

                    $id = $summit_doc->get('id');
                    $conn = sfDoctrine::Connection();
                    try
                    {
                        $conn->beginTransaction();
                        $history_metadata = new HistoryMetadata();
                        $history_metadata->set('is_minor', false);
                        $history_metadata->set('user_id', $this->getUser()->getId());
                        $history_metadata->setComment('Synchronize summit to associated hut');
                        $history_metadata->save();

                        $summit_doc->set('name', $hut_doc->get('name'));
                        $summit_doc->set('lon', $hut_doc->get('lon'));
                        $summit_doc->set('lat', $hut_doc->get('lat'));
                        $summit_doc->set('elevation', $hut_doc->get('elevation'));
                        $summit_doc->save();

                        $conn->commit();

                        if ($geom_changed)
                        {
                            // TODO idea here is to call the refreshGeoAssociations
                            // from summitsActions but we can't call it. In order not to
                            // change the whole mechanism for refreshAssociations, we kinda copy paste
                            // the function here, but this should be improved / factorized
                            // refer to it to understand what is done here
                            $associated_routes = Association::findAllAssociatedDocs($id, array('id', 'geom_wkt'), 'sr');
                            if (count($associated_routes))
                            {
                                $geoassociations = GeoAssociation::findAllAssociations($id, null, 'main');
                                foreach ($associated_routes as $route)
                                {
                                    $i = $route['id'];
                                    if (!$route['geom_wkt'])
                                    {
                                        $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, true);
                                        $this->clearCache('routes', $i, false, 'view');
                                        $associated_outings = Association::findAllAssociatedDocs($i, array('id', 'geom_wkt'), 'ro');
                                        if (count($associated_outings))
                                        {
                                            $geoassociations2 = GeoAssociation::findAllAssociations($i, null, 'main');
                                            foreach ($associated_outings as $outing)
                                            {
                                                $j = $outing['id'];
                                                if (!$outing['geom_wkt'])
                                                {
                                                    $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations2, $j, true, false);
                                                    c2cTools::log("created $nb_created geo associations for outing N° $j");
                                                    $this->clearCache('outings', $j, false, 'view');
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        $conn->rollback();
                        // TODO It is ok to signal the failure, but anyway, the hut doc has been updated
                        // so there is much room for improvement here :)
                        return $this->setErrorAndRedirect("Failed to synchronize summit",
                           '@document_edit?module=huts&id='.$hut_doc->getId().'&lang='.$hut_doc->getCulture());
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
                $hut_lat = $document['lat'];
                $hut_lon = $document['lon'];
                $hut_culture = $document->getCulture();
                $hut_name = $document['name'];

                $history_metadata = new HistoryMetadata();
                $history_metadata->setComment($this->__('Created summit synchronized with hut for access'));
                $history_metadata->set('is_minor', false);
                $history_metadata->set('user_id', 2); // C2C user
                $history_metadata->save();

                $summit = new Summit();
                $summit->setCulture($hut_culture);
                $summit->set('name', $hut_name);
                $summit->set('elevation', $hut_elevation);
                $summit->set('summit_type', 100); // set summit type to ' hut'
                $summit->set('lat', $hut_lat);
                $summit->set('lon', $hut_lon);
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
                        $history_metadata->setComment($this->__('Created summit synchronized with hut for access'));
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
                return $this->setErrorAndRedirect($this->__('Failed to create synchronized summit'), "routes/edit?link=$summit_id");
            }
            
            $summit_id = $summit->get('id');
            
            // get all associated regions (3+maps) with this hut:
            $associations = GeoAssociation::findAllAssociations($id, array('dr', 'dc', 'dd', 'dv', 'dm'));
            // replicate them with summit_id instead of id:
            foreach ($associations as $ea)
            {
                $a = new GeoAssociation();
                $a->doSaveWithValues($summit_id, $ea->get('linked_id'), $ea->get('type'));
            }
            
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
        
        $timer = new sfTimer();
        $huts = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());

        $timer = new sfTimer();
        Parking::addAssociatedParkings($huts, 'ph'); // add associated parkings infos to $huts
        c2cActions::statsdTiming('parking.addAssociatedParkings', $timer->getElapsedTime());

        $timer = new sfTimer();
        Document::countAssociatedDocuments($huts, 'hr', true);
        c2cActions::statsdTiming('document.countAssociatedDocuments', $timer->getElapsedTime());

        Area::sortAssociatedAreas($huts);

        $this->items = Language::parseListItems($huts, 'Hut');
    }
}
