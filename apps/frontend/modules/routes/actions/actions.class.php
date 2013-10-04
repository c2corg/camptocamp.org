<?php
/**
 * routes module actions.
 *
 * @package    c2corg
 * @subpackage routes
 * @version    $Id: actions.class.php 2526 2007-12-18 23:25:31Z alex $
 */
class routesActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Route';
    
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
            $parent_ids = array();

            $main_associated_summits = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_summit')), 'elevation');
            if (count($main_associated_summits))
            {
                foreach ($main_associated_summits as $summit)
                {
                    $parent_ids[] = $summit['id'];
                }
            }
            // routes associated with this route (eg because they share most of the route)
            $associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_routes = $associated_routes;

            // We will display the outings linked to associated routes in a separate section
            // but not for the raids
            $route_ids = array();
            if (count($associated_routes))
            {
                foreach ($associated_routes as $route)
                {
                    if ($route['duration'] instanceof Doctrine_Null or $route['duration'] <= 4)
                    {
                        $route_ids[] = $route['id'];
                    }
                }
            }

            // we will also get parkings linked to linked parkings
            $associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            if (count($associated_parkings))
            {
                foreach ($associated_parkings as $parking)
                {
                    $parent_ids[] = $parking['id'];
                }
            }

            // 2-hops summits, parkings, outings, huts
            $parent_ids = array_merge($parent_ids, $route_ids);
            if (count($parent_ids))
            {
                $associated_childs = Association::findLinkedDocsWithBestName($parent_ids, $prefered_cultures, array('ss', 'pp', 'ro', 'sh'), true, true);
            }
            else
            {
                $associated_childs = array();
            }

            if (count($main_associated_summits))
            {
                $associated_summits = Association::createHierarchy($main_associated_summits,
                    array_filter($associated_childs, array('c2cTools', 'is_summit')),
                    array('type' => 'ss', 'show_sub_docs' => false));
            }
            else
            {
                $associated_summits = $main_associated_summits;
            }

            // directly and indirectly linked huts
            $associated_huts = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_hut')), 'elevation');
            $associated_summit_huts = array_filter($associated_childs, array('c2cTools', 'is_hut'));
            
            // remove the summit if it is linked to a hut
            // because in that case it is a ghost summit of the hut, and
            // shouldn't be displayed
            $summit_huts = array();
            foreach ($associated_summit_huts as $summit_hut)
            {
                foreach ($associated_huts as $key1 => $hut)
                {
                    if ($summit_hut['id'] == $hut['id']) // a hut is both directly linked and linked to a linked summit, the summit is thus a ghost
                    {
                        $linked = array_keys($summit_hut['parent_relation']);
                        $hut['ghost_id'] = array_shift($linked);
                        $summit_huts[] = $hut;
                        unset($associated_huts[$key1]);
                        
                        foreach ($associated_summits as $key2 => $summit)
                        {
                            if ($summit['id'] == $hut['ghost_id'])
                            {
                                unset($associated_summits[$key2]);
                                break;
                            }
                        }
                        
                        break;
                    }
                }
            }
            $this->associated_huts = array_merge($summit_huts, $associated_huts);
            $this->associated_summits = $associated_summits;

            // get all the outings from route and associated routes
            $outing_ids = $associated_routes_outings = array();
            if (count($route_ids))
            {
                $associated_routes_outings = array_filter($associated_childs, array('c2cTools', 'is_outing'));
                if (count($associated_routes_outings))
                {
                    $associated_outings = array_filter($this->associated_docs, array('c2cTools', 'is_outing'));
                    if (count($associated_outings))
                    {
                        foreach ($associated_outings as $outing)
                        {
                            $outing_ids[] = $outing['id'];
                        }
                        foreach ($associated_routes_outings as $outing)
                        {
                            if (!in_array($outing['id'], $outing_ids))
                            {
                                $associated_outings[] = $outing;
                            }
                        }
                    }
                    else
                    {
                        $associated_outings = $associated_routes_outings;
                    }
                }
            }

            array_unshift($route_ids, $current_doc_id);
            $this->ids = implode('-', $route_ids);
            
            if (count($associated_parkings))
            {
                $associated_parkings = Association::createHierarchy($associated_parkings,
                    array_filter($associated_childs, array('c2cTools', 'is_parking')),
                    array('type' => 'pp', 'show_sub_docs' => false));
                $associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
            }
            $this->associated_parkings = $associated_parkings;
            
            // also get author of books
            $associated_books = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_book')), 'name');
            if (count($associated_books))
            {
                $associated_books = Book::getAssociatedBooksData($associated_books);
            }
            $this->associated_books = $associated_books;

            // TODO request will become more and more inefficient as number of linked outings will grow...
            if (!isset($associated_outings))
            {
                $associated_outings = array_filter($this->associated_docs, array('c2cTools', 'is_outing'));
            }
            $associated_outings = Outing::fetchAdditionalFields($associated_outings, true, true);
            // sort outings
            usort($associated_outings, array('c2cTools', 'cmpDate'));
            if (count($associated_routes_outings))
            {
                $main_outings = $routes_outings = array();
                foreach ($associated_outings as $outing)
                {
                    if (in_array($outing['id'], $outing_ids))
                    {
                        $main_outings[] = $outing;
                    }
                    else
                    {
                        $routes_outings[] = $outing;
                    }
                }
            }
            else
            {
                $main_outings = $associated_outings;
                $routes_outings = array();
            }
            
            
            $nb_outings = count($associated_outings);
            $this->nb_outings = count($associated_outings);
            $this->nb_main_outings = count($main_outings);
            $this->nb_routes_outings = count($routes_outings);
            
            // group main_outings  by blocks
            $outings_limit = sfConfig::get('app_users_outings_limit');
            $a = array();
            $i = 0;
            while ($i < 9 && count($main_outings) - $i * $outings_limit > $outings_limit)
            {
                $a[] = array_slice($main_outings, $i * $outings_limit, $outings_limit);
                $i++;
            }
            $a[] = array_slice($main_outings, $i * $outings_limit, $outings_limit);
            $this->associated_outings = $a;
            
            // group routes_outings  by blocks
            $a = array();
            $i = 0;
            while ($i < 0 && count($routes_outings) - $i * $outings_limit > $outings_limit)
            {
                $a[] = array_slice($routes_outings, $i * $outings_limit, $outings_limit);
                $i++;
            }
            $a[] = array_slice($routes_outings, $i * $outings_limit, $outings_limit);
            $this->routes_outings = $a;
            
            // Get related portals
            $related_portals = array();
            $route_data = array();
            $route_data['activities'] = $this->document->get('activities');
            $route_data['ice_rating'] = $this->document->get('ice_rating');
            $route_data['toponeige_technical_rating'] = $this->document->get('toponeige_technical_rating');
            $route_data['global_rating'] = $this->document->get('global_rating');
            $route_data['equipment_rating'] = $this->document->get('equipment_rating');
            $route_data['engagement_rating'] = $this->document->get('engagement_rating');
            $route_data['difficulties_height'] = $this->document->get('difficulties_height');
            $route_data['duration'] = $this->document->get('duration');
            $route_data = array($route_data);
            
            Portal::getRelatedPortals($related_portals, $this->associated_areas, $route_data);
            $this->related_portals = $related_portals;
    
            // extract highest associated summit, and prepend its name to display this route's name.
            $this->highest_summit_name = c2cTools::extractHighestName($main_associated_summits);
            // redefine page title: prepend summit name
            $doc_name = $this->highest_summit_name
                      . $this->__(' :') . ' '
                      . $this->document->get('name');
            $title = $doc_name;
            if ($this->document->isArchive())
            {
                $version = $this->getRequestParameter('version');
                $title .= ' :: ' . $this->__('revision') . ' ' . $version ;
            }
            $doc_type = $this->__('route') . ' / topo';
            $title .= ' :: ' . $doc_type;
            $this->setPageTitle($title);
            $description = array($doc_type . ' :: ' . $doc_name, $this->getActivitiesList(), $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    protected function redirectIfSlugMissing($document, $id, $lang, $module = null)
    {
        // parameter $module is just for compatibility with upper class
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();

        $summits = Association::findAllWithBestName($id, $prefered_cultures, 'sr');
        $summit_name = c2cTools::extractHighestName($summits);

        $slug = make_slug($summit_name) . '-' . get_slug($document);
        $this->redirect("@document_by_id_lang_slug?module=routes&id=$id&lang=$lang&slug=$slug", 301);
    }

    public function executePreview()
    {
        parent::executePreview();

        $id = $this->getRequestParameter('id');

        if (empty($id)) // this is a new route
        {
            $summit_id = $this->getRequestParameter('summit_id');
            if(!empty($summit_id) &&
               $lang = DocumentI18n::findBestCulture($summit_id, $this->getUser()->getCulturesForDocuments(), 'Summit'))
            {
                $this->title_prefix = DocumentI18n::findName($summit_id, $lang, 'Summit');
            }

            $this->associated_books = null;
        }
        else
        {
            $this->title_prefix = $this->getHighestSummitName();

            // retrieve associated books if any
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $this->associated_books = Book::getAssociatedBooksData(
                 Association::findAllWithBestName($id, $prefered_cultures, 'br'));
        }
    }

    public function executeHistory()
    {
        parent::executeHistory();
        $this->title_prefix = $this->getHighestSummitName();
        // redefine page title: prepend summit name
        $this->setPageTitle($this->title_prefix . $this->__(' :') . ' ' . $this->document_name . ' :: ' . $this->__('history'));
    }

    public function setEditFormInformation()
    {
        parent::setEditFormInformation();
        if (!$this->new_document)
        {
            $this->title_prefix = $this->getHighestSummitName();
            $this->setPageTitle($this->__('Edition of "%1%"', array('%1%' => $this->title_prefix . $this->__(' :') . ' ' . $this->document->getName())));

            // retrieve associated books for displaying them near bibliography field
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $id = $this->getRequestParameter('id');
            $this->associated_books = Book::getAssociatedBooksData(Association::findAllWithBestName($id, $prefered_cultures, 'br'));
        }
    }

    public function executeComment()
    {
        parent::executeComment();
        $this->title_prefix = $this->getHighestSummitName();
        $this->setPageTitle($this->title_prefix . $this->__(' :') . ' ' . $this->document_name . ' :: ' . $this->__('Comments'));
    }

    public function executeDiff()
    {
        parent::executeDiff();
        $this->title_prefix = $this->getHighestSummitName();
        $this->setPageTitle($this->title_prefix . $this->__(' :') . ' ' .
                            $this->new_document->get('name') . ' :: ' . $this->__('diff') . ' ' .
                            $this->getRequestParameter('old') . ' > ' . $this->getRequestParameter('new'));
    }

    public function executePopup()
    {
        parent::executePopup();
        $this->title_prefix = $this->getHighestSummitName();
    }

    /** refresh geoassociations of the route and 'sub' outings */
    public function executeRefreshgeoassociations()
    {
        $referer = $this->getRequest()->getReferer();
        $id = $this->getRequestParameter('id');

        // check if user is moderator: done in apps/frontend/config/security.yml

        if (!Document::checkExistence($this->model_class, $id))
        {
            $this->setErrorAndRedirect('Document does not exist', $referer);
        }

        $nb_created = gisQuery::createGeoAssociations($id, true, true);
        c2cTools::log("created $nb_created geo associations");

        $this->refreshGeoAssociations($id);

        $this->clearCache('routes', $id, false, 'view');

        $this->setNoticeAndRedirect('Geoassociations refreshed', "@document_by_id?module=routes&id=$id");
    }

    protected function getHighestSummit()
    {
        $id = $this->getRequestParameter('id');
        if (empty($id)) return null;
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $associated_summits = Association::findAllWithBestName($id, $prefered_cultures, 'sr');
        return c2cTools::extractHighest($associated_summits);
    }

    protected function getHighestSummitName()
    {
        $id = $this->getRequestParameter('id');
        if (empty($id)) return null;
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $associated_summits = Association::findAllWithBestName($id, $prefered_cultures, 'sr');
        // extract highest associated summit, and prepend its name to display this route's name.
        return c2cTools::extractHighestName($associated_summits);
    }

    protected function endEdit()
    {
        //Test if form is submitted or not
        if ($this->success) // form submitted and success (doc has been saved)
        {
            // try to perform association with linked_doc (if pertinent)
            $summit_id = $this->getRequestParameter('summit_id');
            $id = $this->document->get('id');
            $user_id = $this->getUser()->getId();
        
            if (($this->new_document && $summit_id ) || ($summit_id && !Association::find($summit_id, $id, 'sr')))
            {
                $sr = new Association();
                $sr->doSaveWithValues($summit_id, $id, 'sr', $user_id); // main, linked, type
                // clear cache of associated summit ...
                $this->clearCache('summits', $summit_id, false, 'view');
            }        

            // here if we have created a new document and if $this->document->get('geom_wkt') is null, then use associated doc geom associations:
            // this allows us to filter on ranges even if no GPX is uploaded
            if ($this->new_document && $summit_id && !$this->document->get('geom_wkt'))
            {
                // get all associated regions (3+maps) with this summit:
                $associations = GeoAssociation::findAllAssociations($summit_id, array('dr', 'dc', 'dd', 'dv', 'dm'));
                // replicate them with route_id instead of summit_id:
                foreach ($associations as $ea)
                {
                    $a = new GeoAssociation();
                    $a->doSaveWithValues($id, $ea->get('linked_id'), $ea->get('type'));
                }
            }
            // if we add a route to a summit-hut, link the route to the hut
            $hut_asso = Association::findAllAssociations($summit_id, 'sh');
            if ($this->new_document && $summit_id && count($hut_asso) > 0)
            {
                // associate hut to summit 
                $asso = new Association();
                $hut_id = $hut_asso[0]->get('linked_id');
                $asso->doSaveWithValues($hut_id, $id, 'hr', 2); // C2C user
                // clear cache of associated hut ...
                $this->clearCache('huts', $hut_id, false, 'view');
            }

            parent::endEdit(); // redirect to document view
        }
        else //  We want to display summit name before route title input in the form
        {
            if ($this->link_with = $this->getRequestParameter('link')) // new route, linked summit id is in link parameter
            {
                // form viewing => get linked doc
                $linked_doc = Document::find('Summit', $this->link_with, array('id', 'module'));
            
                if ($linked_doc)
                {
                    $linked_doc->setBestCulture($this->getUser()->getCulturesForDocuments());
                    $this->linked_doc = $linked_doc;
                }
            }
            else // existing route, we try to find the best summit to display
            {
                $this->linked_doc =  $this->getHighestSummit(); 
            }
        }
    }

    /**
     * overrides function from parent in order to correctly display slug
     * with summit name
     */
    protected function redirectToView()
    {
        sfLoader::loadHelpers(array('General'));
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $summits = Association::findAllWithBestName($this->document->get('id'), $prefered_cultures, 'sr');
        $summit_name = c2cTools::extractHighestName($summits);

        $this->redirect('@document_by_id_lang_slug?module=' . $this->getModuleName() .
                        '&id=' . $this->document->get('id') .
                        '&lang=' . $this->document->getCulture() .
                        '&slug=' . make_slug($summit_name) . '-' .get_slug($this->document));
    }

    /**
     * This function is used to get a DB query result formatted in HTML. It is used
     * from the generic action class (in the documents module)
     */
    protected function getFormattedResult($result) {

        // Explicitely load helpers (required in the controller)        
        sfLoader::loadHelpers(array('Tag', 'Url', 'Javascript'));
        
        $list = sfConfig::get('mod_routes_facings_list');
        
        $html  = '<td>' . link_to($result['name'], '@document_by_id?module=routes&id=' . $result['id']) . '</td>';
        $html .= '<td>' . $result['min_elevation'] . '</td>';
        $html .= '<td>' . $result['max_elevation'] . '</td>';
        $html .= '<td>' . $list[$result['facing']] . '</td>';

        return $html;
    }

    public function executeGetratings()
    {
        $id = $this->getRequestParameter('id');
     
        if (!$id)
        {
            return $this->ajax_feedback('Missing id parameter');
        }
//TODO 
        $fields = array('activities', 'facing', 'height_diff_up', 'global_rating', 'engagement_rating', 'objective_risk_rating',
                        'toponeige_technical_rating', 'toponeige_exposition_rating', 'labande_ski_rating',
                        'labande_global_rating', 'rock_free_rating', 'ice_rating', 'mixed_rating', 
                        'aid_rating', 'rock_exposition_rating', 'hiking_rating', 'snowshoeing_rating');
         
        $this->data = Document::find('Route', $id, $fields);
        if (!$this->data)
        {
            return $this->ajax_feedback('not available'); 
        }
    }
    
    /**
     * filter edits which must require additional parameters (link for instance : route with summit)
     * overrides the one in parent class
     */
    protected function filterAdditionalParameters()
    {
        if (!$this->getRequestParameter('summit_id') && !$this->getRequestParameter('link'))
        {
            $this->setErrorAndRedirect('You cannot create a route without linking it to an existing summit', '@default_index?module=routes');
        }
        
        $id = $this->getRequestParameter('link', 0) + $this->getRequestParameter('summit_id', 0);
        
        $linked_doc = Document::find('Summit', $id, array('id', 'module'));
        if (!$linked_doc)
        {
            $this->setErrorAndRedirect('You cannot create a route without linking it to an existing summit', '@default_index?module=routes');
        }
        
    }

    /**
     * Overriddes the one in parent class 
     * this is because we sometimes have to do things when centroid coordinates have moved.
     */
    protected function refreshGeoAssociations($id)
    {    
        c2cTools::log("Entering refreshGeoAssociations for outings linked with route $id");
        
        $associated_outings = Association::findAllAssociatedDocs($id, array('id', 'geom_wkt'), 'ro');
        
        if (count($associated_outings))
        {
            $geoassociations = GeoAssociation::findAllAssociations($id, null, 'main');
            // we create new associations :
            //  (and delete old associations before creating the new ones)
            //  (and do not create outings-maps associations)        
            foreach ($associated_outings as $outing)
            {
                $i = $outing['id'];
            
                if (!$outing['geom_wkt']) // proof that there is no pre-existing geoassociation due to a GPX upload
                {
                    // replicate geoassoces from doc $id to outing $i and delete previous ones 
                    // (because there might be geoassociations created by this same process)
                    // and we do not replicate map associations to outings
                    $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, false);
                    c2cTools::log("created $nb_created geo associations for outing N° $i");
                    $this->clearCache('outings', $i, false, 'view');
                }
            }
        }
    }

    protected function filterSearchParameters()
    {
        $out = array();
        
        if($this->getUser()->isConnected())
        {
            $myroutes = $this->getRequestParameter('myroutes', 0);
            if ($myroutes > 0)
            {
                $user_id = $this->getUser()->getId();
                if ($myroutes == 1)
                {
                    $myroutes_param = "users";
                }
                else
                {
                    $myroutes_param = "nousers";
                }
                $out[] = "$myroutes_param=$user_id";
            }
        }

        $this->addListParam($out, 'areas');
        $this->addAroundParam($out, 'parnd');

        $this->addNameParam($out, 'snam');
        $this->addCompareParam($out, 'salt');
        $this->addParam($out, 'styp');
        $this->addListParam($out, 'stags');

        $this->addNameParam($out, 'hnam');
        $this->addCompareParam($out, 'halt');
        $this->addParam($out, 'hsta');
        
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');

        $this->addNameParam($out, 'rnam');
        $this->addCompareParam($out, 'malt');
        $this->addCompareParam($out, 'hdif');
        $this->addCompareParam($out, 'ralt');
        $this->addCompareParam($out, 'dhei');
        $this->addListParam($out, 'act');
        $this->addListParam($out, 'sub', '', '', true);
        $this->addListParam($out, 'conf');
        $this->addFacingParam($out, 'fac');
        $this->addListParam($out, 'rtyp');
        $this->addCompareParam($out, 'time');
        $this->addCompareParam($out, 'trat');
        $this->addCompareParam($out, 'sexpo');
        $this->addCompareParam($out, 'lrat');
        $this->addCompareParam($out, 'srat');
        $this->addCompareParam($out, 'grat');
        $this->addCompareParam($out, 'erat');
        $this->addCompareParam($out, 'orrat');
        $this->addCompareParam($out, 'prat');
        $this->addCompareParam($out, 'irat');
        $this->addCompareParam($out, 'mrat');
        $this->addCompareParam($out, 'frat');
        $this->addCompareParam($out, 'rrat');
        $this->addCompareParam($out, 'arat');
        $this->addCompareParam($out, 'rexpo');
        $this->addCompareParam($out, 'hrat');
        $this->addCompareParam($out, 'wrat');
        $this->addCompareParam($out, 'rlen');
        $this->addParam($out, 'glac');
        $this->addParam($out, 'geom');
        $this->addParam($out, 'rcult');

        return $out;
    }

    /**
     * Executes list action, adding parkings linked to routes
     */
    public function executeList()
    {
        parent::executeList();

        $nb_results = $this->nb_results;
        if ($nb_results == 0) return;

        $timer = new sfTimer();
        $routes = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());

        // if they are criterias on the summit (snam, srnam, salt, styp)
        // we might have only some of the associated summits and not the 'best one' (ticket #337)
        // so we must add a new request to get the summits, display the best one and add a note to explain that the
        // other summit is associated
        // FIXME would be nice to put all in a single request (before), but I didn't manage to do it
        // TODO not working right now
        if ($this->hasRequestParameter('snam') || $this->hasRequestParameter('srnam') ||
            $this->hasRequestParameter('salt') || $this->hasRequestParameter('styp'))
        {
           // $routes = Route::addBestSummitName($routes, '');
        }

        $timer = new sfTimer();
        Parking::addAssociatedParkings($routes, 'pr'); // add associated parkings infos to $routes
        c2cActions::statsdTiming('parking.addAssociatedParkings', $timer->getElapsedTime());

        $timer = new sfTimer();
        Document::countAssociatedDocuments($routes, 'ro', true); // number of associated outings
        c2cActions::statsdTiming('document.countAssociatedDocuments', $timer->getElapsedTime());

        Area::sortAssociatedAreas($routes);

        $this->items = Language::parseListItems($routes, 'Route');
    }
}
