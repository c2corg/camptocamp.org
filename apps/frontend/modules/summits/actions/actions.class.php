<?php
/**
 * summits module actions.
 *
 * @package    c2corg
 * @subpackage summits
 * @version    $Id: actions.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class summitsActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Summit';
    
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

            // ghost summits, used for adding routes to huts
            // if summit is associated directly to a hut, redirect to hut unless ?redirect=no is appended (after a slug!)
            $associated_huts = array_filter($this->associated_docs, array('c2cTools', 'is_hut'));
            if (count($associated_huts) > 0 && $this->getRequestParameter('redirect') != 'no')
            {
                $associated_hut = reset($associated_huts); // array has been filtered
                $hut_id = $associated_hut['id'];
                $lang = $this->getRequestParameter('lang');
                $this->redirect("@document_by_id_lang?module=huts&id=$hut_id&lang=$lang");
            }

            // main associated summits are summits directly linked to this one
            $main_associated_summits = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_summit')), 'elevation');
            $associated_sites = $this->associated_sites;

            // idea here is to display some docs (routes, images, sites), not only if they are directly linked to the summit,
            // but also if they are linked to a sub(-sub)-summit
            $summit_ids = array();
            if (count($main_associated_summits))
            {
                $associated_summits = Association::createHierarchyWithBestName($main_associated_summits, $prefered_cultures,
                    array('type' => 'ss', 'current_doc_id' => $current_doc_id, 'keep_current_doc' => true));

                // simply go through the list and get the next items that have a bigger level
                $i = reset($associated_summits);
                while(!isset($i['is_doc']))
                {
                    $i = next($associated_summits);
                }
                $doc_level = $i['level'];
                $i = next($associated_summits);
                while($i !== false && $i['level'] > $doc_level)
                {
                    $summit_ids[] = $i['id'];
                    $i = next($associated_summits);
                }

                if (count($summit_ids))
                {
                    $summit_docs = array_filter($this->associated_docs, array('c2cTools', 'is_site_route_image'));
                    $summit_docs_ids = array();
                    foreach ($summit_docs as $doc)
                    {
                        $summit_docs_ids[] = $doc['id'];
                    }
                    $associated_summit_docs = Association::findLinkedDocsWithBestName($summit_ids, $prefered_cultures, array('st', 'sr', 'si'),
                        false, true, $summit_docs_ids);

                    $this->associated_docs = array_merge($this->associated_docs, $associated_summit_docs);
                    
                    $associated_summit_sites = c2cTools::sortArrayByName(array_filter($associated_summit_docs, array('c2cTools', 'is_site')));
                    foreach ($associated_summit_sites as $key => $site)
                    {
                        $associated_summit_sites[$key]['parent_id'] = true;
                    }
                    $associated_sites = array_merge($associated_sites, $associated_summit_sites);
                    $this->associated_sites = $associated_sites;
                }
            }
            else
            {
                $associated_summits = $main_associated_summits;
            }
            
            $this->associated_summits = $associated_summits;
            array_unshift($summit_ids, $current_doc_id);
            $this->ids = implode('-', $summit_ids);
            
            // second param will not display the summit name before the route when the summit is the one of the document
            $associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ', $this->document->get('id'), $this->document->get('name'));
            $this->associated_routes = $associated_routes;

            $associated_books = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_book')));
            
            $doc_ids = array();
            $associated_huts = array();
            $associated_parkings = array();
            $associated_routes_books = array();
            if (count($associated_routes) || count($associated_sites))
            {
                foreach ($associated_routes as $route)
                {
                    if ($route['duration'] instanceof Doctrine_Null || $route['duration'] <= 4)
                    {
                        $doc_ids[] = $route['id'];
                    }
                }
                
                foreach ($associated_sites as $site)
                {
                    $doc_ids[] = $site['id'];
                }
                
                if (count($doc_ids))
                {
                    $book_ids = array();
                    foreach ($associated_books as $book)
                    {
                        $book_ids[] = $book['id'];
                    }
                    $associated_route_docs = Association::findLinkedDocsWithBestName($doc_ids, $prefered_cultures, array('hr', 'ht', 'pr', 'pt', 'br'),
                        false, false, $book_ids);

                    if (count($associated_route_docs))
                    {
                        $associated_route_docs = c2cTools::sortArray($associated_route_docs, 'elevation');
                        $associated_huts = array_filter($associated_route_docs, array('c2cTools', 'is_hut'));
                        $associated_parkings = Parking::getAssociatedParkingsData(array_filter($associated_route_docs,
                            array('c2cTools', 'is_parking')));

                        $associated_routes_books = c2cTools::sortArray(array_filter($associated_route_docs, array('c2cTools', 'is_book')), 'name');
                        foreach ($associated_routes_books as $key => $book)
                        {
                            $associated_routes_books[$key]['parent_id'] = true;
                        }
                    }
                }
            }
            $this->associated_huts = $associated_huts;
            $this->associated_parkings = $associated_parkings;
            
            $associated_books = array_merge($associated_books, $associated_routes_books);
            if (count($associated_books))
            {
                $associated_books = Book::getAssociatedBooksData($associated_books);
            }
            $this->associated_books = $associated_books;
            
            // get associated outings
            $latest_outings = array();
            $nb_outings = 0;
            if (count($associated_routes))
            {
                $outing_params = array('summits' => $this->ids);
                $nb_outings = sfConfig::get('app_nb_linked_outings_docs');
                $latest_outings = Outing::listLatest($nb_outings + 1, array(), array(), array(), $outing_params, false);
                $latest_outings = Language::getTheBest($latest_outings, 'Outing');
            }
            $this->latest_outings = $latest_outings;
            $this->nb_outings = $nb_outings;
            
            $this->associated_images = Document::fetchAdditionalFieldsFor(
                                        array_filter($this->associated_docs, array('c2cTools', 'is_image')), 
                                        'Image', 
                                        array('filename', 'image_type', 'date_time', 'width', 'height'));
            
            $cab = count($associated_books);
            $this->section_list = array('books' => ($cab != 0), 'map' => (boolean)$this->document->get('geom_wkt'));
            
            $related_portals = array();
            Portal::getRelatedPortals($related_portals, $this->associated_areas, $associated_routes);
            $summit_type_index = $this->document->get('summit_type');
            if ($summit_type_index == 5 && !in_array('raid', $related_portals))
            {
                $related_portals[] = 'raid';
            }
            $this->related_portals = $related_portals;
    
            $summit_type_list = sfConfig::get('app_summits_summit_types');
            $summit_type_list[1] = 'summit';
            $summit_type = $this->__($summit_type_list[$summit_type_index]);
            $doc_name = $this->document->get('name');
            
            $title = $doc_name;
            if ($this->document->isArchive())
            {
                $version = $this->getRequestParameter('version');
                $title .= ' :: ' . $this->__('revision') . ' ' . $version ;
            }
            $title .= ' :: ' . $summit_type;
            $this->setPageTitle($title);

            $description = array($summit_type . ' :: ' . $doc_name,
                                 $this->document->get('elevation') . $this->__('meters'),
                                 $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePopup()
    {
        parent::executePopup();
        $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ', $this->document->get('id'), $this->document->get('name'));
    }

    /**
     * This function is used to get a DB query result formatted in HTML. It is used
     * from the generic action class (in the documents module)
     */
    protected function getFormattedResult($result) {

        // Explicitely load helpers (required in the controller)        
        sfLoader::loadHelpers(array('Tag', 'Url', 'Javascript'));
        
        $html  = '<td>' . link_to($result['name'], '@document_by_id?module=summits&id=' . $result['id']) . '</td>';
        $html .= '<td>' . $result['elevation'] . '</td>';

        return $html;
    }
    
    /**
     * returns a list of associated routes
     */
    public function executeGetroutes()
    {
        $id = $this->getRequestParameter('summit_id');
        $div_name = $this->getRequestParameter('div_name');
        $div_prefix = $this->getRequestParameter('div_prefix', '');
        $div_id = $div_prefix.$div_name;

        $user = $this->getUser();
        $user_id = $user->getId(); 
        
        // if id = 0 or no provided
        if (!$id)
        {
            return $this->ajax_feedback('Missing id parameter');
        }
        
        // if session is time-over
        if (!$user_id)
        {
            return $this->ajax_feedback('Your session is over. Please login again.');
        }
        
        $summit = Document::find('Summit', $id, array('id', 'elevation')); 
        if (!$summit)
        {
            return $this->ajax_feedback('Summit not found'); 
        }
        
        $sub_summits = Summit::getSubSummits($id);
        $ids = array($id);
        foreach ($sub_summits as $sub)
        {
            $ids[] = $sub['id'];
        }
        
        $routes = Association::findLinkedDocsWithBestName($ids, $this->getUser()->getCulturesForDocuments(), 'sr');
        $routes = Route::addBestSummitName($routes, $this->__('&nbsp;:') . ' ');
        $routes = c2cTools::sortArrayByName($routes);
        
        if (count($routes) == 0)
        {
            return $this->ajax_feedback($this->__('No associated route found'));
        }

        if (!$div_id)
        {
            return $this->ajax_feedback('Please chose a "select" container ID in "remote_function"');
        }

        $output = $this->__('Route:') . ' <select id="' . $div_id . '" name="' . $div_name . '" onchange="C2C.getWizardRouteRatings(\'' . $div_id . '\');">';
        foreach ($routes as $route)
        {
            $output .= '<option value="' . $route['id'] . '">' . $route['name'] . '</option>';
        }
        $output .= '</select>';
        
        $output .= '<p id="wizard_' . $div_id . '_descr" class="short_descr" style="display: none">'
                 . '<span id="' . $div_id . '_descr">' . $this->__('Short description: ')
                 . $this->__('not available') . '</span></p>';
        
        return $this->renderText($output);
    }

    /** summits: refresh geo associations of 'sub' routes and outings (action for moderators only) */
    public function executeRefreshgeoassociations()
    {
        $referer = $this->getRequest()->getReferer();
        $id = $this->getRequestParameter('id');

        // check if user is moderator: done in apps/frontend/config/security.yml

        $this->document = Document::find($this->model_class, $id, array('summit_type'));

        if (!$this->document)
        {
            $this->setErrorAndRedirect('Document does not exist', $referer);
        }

        $nb_created = gisQuery::createGeoAssociations($id, true, true);
        c2cTools::log("created $nb_created geo associations");

        $this->refreshGeoAssociations($id);

        $this->clearCache('summits', $id, false, 'view');
        
        $this->setNoticeAndRedirect('Geoassociations refreshed', "@document_by_id?module=summits&id=$id");
    }

    /** summits: refresh geo associations of summit, 'sub' routes and outings */
    public function updateGeoAssociations($id)
    {
        $referer = $this->getRequest()->getReferer();

        $this->document = Document::find($this->model_class, $id, array('summit_type'));

        if (!$this->document)
        {
            $this->setErrorAndRedirect('Document does not exist', $referer);
        }

        $nb_created = gisQuery::createGeoAssociations($id, true, true);
        c2cTools::log("created $nb_created geo associations");

        $this->refreshGeoAssociations($id);

        $this->clearCache('summits', $id, false, 'view');
        
        $this->setNoticeAndRedirect('Geoassociations refreshed', "@document_by_id?module=summits&id=$id");
    }
    
    /**
     * Overriddes the one in parent class 
     * this is because we sometimes have to do things when centroid coordinates have moved.
     * TODO hutsActions::endEdit() should be factorized with this..
     */
    protected function refreshGeoAssociations($id)
    {    
        // don't refresh associated doc if summit type is "raid"
        if ($this->document->get('summit_type') == 5)
            return;
        
        c2cTools::log("Entering refreshGeoAssociations for routes linked with summit $id");
        
        $associated_routes = Association::findAllAssociatedDocs($id, array('id', 'geom_wkt'), 'sr');
        
        if (count($associated_routes))
        {
            $geoassociations = GeoAssociation::findAllAssociations($id, null, 'main'); 
            // we create new associations :
            //  (and delete old associations before creating the new ones)
            //  (and do not create outings-maps associations)        
            foreach ($associated_routes as $route)
            {
                $i = $route['id'];
            
                if (!$route['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                {
                    // replicate geoassoces from doc $id to outing $i and delete previous ones 
                    // (because there might be geoassociations created by this same process)
                    $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, true);
                    c2cTools::log("created $nb_created geo associations for route N° $i");
                    $this->clearCache('routes', $i, false, 'view');
 
                    $associated_outings = Association::findAllAssociatedDocs($i, array('id', 'geom_wkt'), 'ro');
        
                    if (count($associated_outings))
                    {
                        $geoassociations2 = GeoAssociation::findAllAssociations($i, null, 'main'); 
                        // we create new associations :
                        //  (and delete old associations before creating the new ones)
                        //  (and do not create outings-maps associations)        
                        foreach ($associated_outings as $outing)
                        {
                            $j = $outing['id'];
            
                            if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                            {
                                // replicate geoassoces from doc $id to outing $i and delete previous ones 
                                // (because there might be geoassociations created by this same process)
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
    
    /**
     * Parses REQUEST sent by filter form and keeps only relevant parameters.
     * @return array
     */
    protected function filterSearchParameters()
    {
        $out = array();
        
        $this->addListParam($out, 'areas');
        $this->addNameParam($out, 'snam');
        $this->addCompareParam($out, 'salt');
        $this->addAroundParam($out, 'sarnd');
        $this->addListParam($out, 'styp');
        $this->addParam($out, 'geom');
        $this->addListParam($out, 'stags');
        $this->addListParam($out, 'act');
        $this->addParam($out, 'bbox');
        $this->addListParam($out, 'scult');
        
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
        $summits = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());

        $timer = new sfTimer();
        Document::countAssociatedDocuments($summits, 'sr', true);
        c2cActions::statsdTiming('document.countAssociatedDocuments', $timer->getElapsedTime());

        Area::sortAssociatedAreas($summits);

        $this->items = Language::parseListItems($summits, 'Summit');
    }


    /**
     * Executes delete action
     * Checks if there are some associated routes with only this summit.
     * If not, then call the parent default delete action. 
     * If yes, calls error and redirect
     */
    public function executeDelete()
    {
        $referer = $this->getRequest()->getReferer();
        if ($id = $this->getRequestParameter('id'))
        {
            // Get all associated documents
            $associated_docs = Association::findAllAssociatedDocs($id, array('id', 'module'));

            if ( $associated_docs )
            {
                // Initialise the list of routes only associated to this summit
                $single_summit_route_list = array();

                // Check if any associated doc is a route
                foreach( $associated_docs as $doc )
                {
                    if ( $doc['module'] == 'routes' )
                    {
                        // if we found an associated route, check if it is associated to several summits
                        $route_associated_docs = Association::findAllAssociatedDocs($doc['id'], array('id', 'module'));
                        if ( $route_associated_docs )
                        {
                            // Check if any associated doc to the route is a summit different from the one we want to delete
                            $multiple_summit = False;
                            foreach( $route_associated_docs as $route_associated_doc )
                            {
                                // There's an associated summit which is different from the one we want to delete
                                if ( ($route_associated_doc['module'] == 'summits') && ($route_associated_doc['id'] != $id) )
                                {
                                    $multiple_summit = True;
                                    break;
                                }
                            }

                            if ( ! $multiple_summit )
                            {
                                // this route has only one summit: the one we want to delete
                                $single_summit_route_list[] = $doc['id'];
                            }
                        }
                        else
                        {
                            // shouldn't reach here, as the route we found should at least be associated to the summit that should (or not) be deleted
                            $this->setErrorAndRedirect('Document could not be deleted', $referer);
                        }
                    }
                }

                if ( ! empty( $single_summit_route_list ) )
                {
                    // If we found an associated route which has only one summit, we do not delete the summit
                    $this->setErrorAndRedirect('Document could not be deleted because there would be orphean routes%1%', $referer, array('%1%' => '<li><ul>'.implode('</ul><ul>', $single_summit_route_list).'</ul></li>'));
                }
            }
            // If we reach here, then either there were no associated docs, or none of them was a route.
            parent::executeDelete();
        }
        else
        {
            $this->setErrorAndRedirect('Could not understand your request', $referer);
        }
    }
}
