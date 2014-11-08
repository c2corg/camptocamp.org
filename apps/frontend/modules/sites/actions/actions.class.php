<?php
/**
 * sites module actions.
 *
 * @package    c2corg
 * @subpackage sites
 * @version    $Id: actions.class.php 2541 2007-12-20 18:17:11Z alex $
 */
class sitesActions extends documentsActions
{
    /**
     * Model class name.
     */
    protected $model_class = 'Site';

    /**
     * Nb of dimensions for geom column
     */   
    protected $geom_dims = 3; 
    
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
            $parent_ids = $sub_site_ids = $site_docs_ids = $child_types = array();

            // if we have sub-(sub)-sites, we also want to display the outings and images linked to these sites
            $main_associated_sites = $this->associated_sites;
            if (count($main_associated_sites))
            {
                $associated_sites = Association::createHierarchyWithBestName($main_associated_sites, $prefered_cultures,
                    array('type' => 'tt', 'current_doc_id' => $current_doc_id, 'keep_current_doc' => true));

                $i = reset($associated_sites);
                while(!isset($i['is_doc']))
                {
                    $i = next($associated_sites);
                }
                $doc_level = $i['level'];
                $i = next($associated_sites);
                while($i !== false && $i['level'] > $doc_level)
                {
                    $sub_site_ids[] = $i['id'];
                    $i = next($associated_sites);
                }

                // we want to display on the page the images and outings of the subsites
                if (count($sub_site_ids))
                {
                    $site_docs = array_filter($this->associated_docs, array('c2cTools', 'is_image'));
                    foreach ($site_docs as $doc)
                    {
                        $site_docs_ids[] = $doc['id'];
                    }
                    $child_types[] = 'ti';
                    $child_types[] = 'to';
                }
            }
            else
            {
                $associated_sites = $main_associated_sites;
            }

            $associated_summits = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_summit')), 'elevation');

            // we display sub-sub-sites one the page, but also the others sites linked to the same summits
            if (count($associated_summits))
            {
                foreach ($associated_summits as $summit)
                {
                    $summit_ids[] = $summit['id'];
                }
                $sites_ids = array();
                foreach ($associated_sites as $site)
                {
                    $sites_ids[] = $site['id'];
                }
                $summit_docs_ids = array_merge($sites_ids, array($current_doc_id));
                $associated_summits_sites = Association::findLinkedDocsWithBestName($summit_ids, $prefered_cultures, 'st', true, true, $summit_docs_ids);
                $associated_sites = array_merge($associated_sites, $associated_summits_sites);
            }

            // associated parkings 2-hop hierarchy
            $associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            if (count($associated_parkings))
            {
                foreach ($associated_parkings as $parking)
                {
                    $parent_ids[] = $parking['id'];
                }
                $child_types[] = 'pp';
            }
            
            $associated_outings = array_filter($this->associated_docs, array('c2cTools', 'is_outing'));

            // all outings (directly or indirectly linked) 
            $parent_ids = array_merge($parent_ids, $sub_site_ids);
            if (count($parent_ids)) // "sites" can have no linked doc
            {
                $associated_childs = Association::findLinkedDocsWithBestName($parent_ids, $prefered_cultures, $child_types, true, true, $site_docs_ids);
                $this->associated_docs = array_merge($this->associated_docs, $associated_childs);
            
                if (count($associated_parkings))
                {
                    $associated_parkings = Association::createHierarchy($associated_parkings,
                        array_filter($associated_childs, array('c2cTools', 'is_parking')),
                        array('type' => 'pp', 'show_sub_docs' => false));
                }
                
                if (count($sub_site_ids))
                {
                    $associated_site_outings = array_filter($associated_childs, array('c2cTools', 'is_outing'));
                    if (count($associated_site_outings))
                    {
                        if (count($associated_outings))
                        {
                            $outing_ids = array();
                            foreach ($associated_outings as $outing)
                            {
                                $outing_ids[] = $outing['id'];
                            }
                            foreach ($associated_site_outings as $outing)
                            {
                                if (!in_array($outing['id'], $outing_ids))
                                {
                                    $associated_outings[] = $outing;
                                }
                            }
                        }
                        else
                        {
                            $associated_outings = $associated_site_outings;
                        }
                    }
                }
            }

            $this->associated_sites = $associated_sites;
            
            array_unshift($sub_site_ids, $current_doc_id);
            $this->ids = implode('-', $sub_site_ids);
            
            $this->associated_parkings = Parking::getAssociatedParkingsData($associated_parkings);
            
            $this->associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_huts = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_hut')), 'elevation');
            $this->associated_summits = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_summit')), 'elevation');
            
            // also get author of books
            $associated_books = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_book')), 'name');
            if (count($associated_books))
            {
                $associated_books = Book::getAssociatedBooksData($associated_books);
            }
            $this->associated_books = $associated_books;

            if (!isset($associated_outings))
            {
                $associated_outings = array_filter($this->associated_docs, array('c2cTools', 'is_outing'));
            }
            $associated_outings = Outing::fetchAdditionalFields($associated_outings, true, true);
            // sort outings array by antichronological order.
            usort($associated_outings, array('c2cTools', 'cmpDate'));
            $this->nb_outings = count($associated_outings);
            // group them by blocks
            $outings_limit = sfConfig::get('app_documents_outings_limit');
            $a = array();
            $i = 0;
            while (count($associated_outings) - $i*$outings_limit > $outings_limit)
            {
                $a[] = array_slice($associated_outings, $i * $outings_limit, $outings_limit);
                $i++;
            }
            $a[] = array_slice($associated_outings, $i * $outings_limit);
            $this->associated_outings = $a;
    
            $this->associated_images = Document::fetchAdditionalFieldsFor(
                                        array_filter($this->associated_docs, array('c2cTools', 'is_image')), 
                                        'Image', 
                                        array('filename', 'image_type', 'date_time', 'width', 'height'));
            
            
            $site_types = $this->document->get('site_types');
            if (!is_array($site_types))
            {
                $site_types = Document::convertStringToArray($site_types);
            }
            $site_types_list = sfConfig::get('app_sites_site_types');
            foreach ($site_types as &$type)
            {
                $type = $this->__($site_types_list[$type]);
            }
            $site_types = implode(', ', $site_types);
            if (!empty($site_types))
            {
                $site_types = ' (' . $site_types . ')';
            }
            $site_types = $this->__('site') . $site_types;
            $doc_name = $this->document->get('name');
            
            $title = $doc_name;
            if ($this->document->isArchive())
            {
                $version = $this->getRequestParameter('version');
                $title .= ' :: ' . $this->__('revision') . ' ' . $version ;
            }
            
            $title .= ' :: ' . $site_types;
            $this->setPageTitle($title);

            $description = array($site_types . ' :: ' . $doc_name,
                                 $this->getAreasList());
            $this->getResponse()->addMeta('description', implode(' - ', $description));
        }
    }

    public function executePreview()
    {
        parent::executePreview();

        $id = $this->getRequestParameter('id');

        if (empty($id)) // new site
        {
            $this->associated_books = null;
        }
        else
        {
            // retrieve associated books if any
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $this->associated_books = Book::getAssociatedBooksData(
                 Association::findAllWithBestName($id, $prefered_cultures, 'bt'));
        }
    }

    public function setEditFormInformation()
    {
        parent::setEditFormInformation();
        if (!$this->new_document)
        {
            // retrieve associated books for displaying them near bibliography field
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $id = $this->getRequestParameter('id');
            $this->associated_books = Book::getAssociatedBooksData(Association::findAllWithBestName($id, $prefered_cultures, 'bt'));
        }
    }

    /** refresh geoassociations of the route and 'sub' outings */
    public function executeRefreshgeoassociations()
    {
        $referer = $this->getRequest()->getReferer();
        $id = $this->getRequestParameter('id');

        // check if user is moderator: done in apps/frontend/config/security.yml

        if (!Document::find($this->model_class, $id))
        {
            $this->setErrorAndRedirect('Document does not exist', $referer);
        }

        $nb_created = gisQuery::createGeoAssociations($id, true, true);
        c2cTools::log("created $nb_created geo associations");

        $this->refreshGeoAssociations($id);

        $this->clearCache('sites', $id, false, 'view');

        $this->setNoticeAndRedirect('Geoassociations refreshed', "@document_by_id?module=sites&id=$id");
    }

    /**
     * This function is used to get a DB query result formatted in HTML. It is used
     * from the generic action class (in the documents module)
     */
    protected function getFormattedResult($result) {

        // Explicitely load helpers (required in the controller)        
        sfLoader::loadHelpers(array('Tag', 'Url', 'Javascript'));
        
        $list = sfConfig::get('mod_sites_rock_free_ratings_list');

        $elevation = isset($result['elevation']) ? $result['elevation'] : '-';
        $routes_quantity = isset($result['routes_quantity']) ? $result['routes_quantity'] : '-';
        $min_rating = isset($result['min_rating']) ? $this->__($list[$result['min_rating']]) : '-';
        $max_rating = isset($result['max_rating']) ? $this->__($list[$result['max_rating']]) : '-';

        $html  = '<td>' . link_to($result['name'], '@document_by_id?module=sites&id=' . $result['id']) . '</td>';
        $html .= '<td>' . $elevation . '</td>';
        $html .= '<td>' . $routes_quantity . '</td>';
        $html .= '<td>' . $min_rating . '</td>';
        $html .= '<td>' . $max_rating . '</td>';

        return $html;
    }
    
    /**
     * Overriddes the one in parent class 
     * this is because we sometimes have to do things when centroid coordinates have moved.
     */
    protected function refreshGeoAssociations($id)
    {    
        c2cTools::log("Entering refreshGeoAssociations for outings linked with site $id");
        
        $associated_outings = Association::findAllAssociatedDocs($id, array('id', 'geom_wkt'), 'to');
        
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
                    $nb_created = GeoAssociation::replicateGeoAssociations($geoassociations, $i, true, false);
                    c2cTools::log("created $nb_created geo associations for outing N° $i");
                    $this->clearCache('outings', $i, false, 'view');
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
        $this->addAroundParam($out, 'tarnd');
        
        $this->addNameParam($out, 'pnam');
        $this->addCompareParam($out, 'palt');
        $this->addListParam($out, 'tp');
        $this->addListParam($out, 'tpty');

        $this->addNameParam($out, 'tnam');
        $this->addCompareParam($out, 'talt');
        $this->addListParam($out, 'ttyp');
        $this->addListParam($out, 'tcsty');
        $this->addCompareParam($out, 'tprat');
        $this->addCompareParam($out, 'rqua');
        $this->addCompareParam($out, 'tmhei');
        $this->addCompareParam($out, 'tmrat');
        $this->addListParam($out, 'tfac');
        $this->addListParam($out, 'trock');
        $this->addListParam($out, 'chil');
        $this->addListParam($out, 'rain');
        $this->addParam($out, 'geom');
        $this->addParam($out, 'tcult');

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
        $sites = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
        c2cActions::statsdTiming('pager.getResults', $timer->getElapsedTime());

        $timer = new sfTimer();
        Parking::addAssociatedParkings($sites, 'pt'); // add associated parkings infos to $sites
        c2cActions::statsdTiming('parking.addAssociatedParkings', $timer->getElapsedTime());

        $timer = new sfTimer();
        Document::countAssociatedDocuments($sites, 'to', true);
        c2cActions::statsdTiming('document.countAssociatedDocuments', $timer->getElapsedTime());

        Area::sortAssociatedAreas($sites);

        $this->items = Language::parseListItems($sites, 'Site');
    }
}
