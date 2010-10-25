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
            
            $associated_routes = Route::getAssociatedRoutesData($this->associated_docs, $this->__(' :').' ');
            $this->associated_routes = $associated_routes;

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
            
            $associated_parkings = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_parking')), 'elevation');
            if (count($associated_parkings))
            {
                foreach ($associated_parkings as $parking)
                {
                    $parent_ids[] = $parking['id'];
                }
            }
            
            $parent_ids = array_merge($parent_ids, $route_ids);
            if (count($parent_ids))
            {
                $associated_childs = Association::findWithBestName($parent_ids, $prefered_cultures, array('ss', 'pp', 'ro'), true, true);
            }
            else
            {
                $associated_childs = array();
            }
            
            if (count($main_associated_summits))
            {
                $associated_summits = Association::addChild($main_associated_summits, array_filter($associated_childs, array('c2cTools', 'is_summit')), 'ss', null, false);
            }
            else
            {
                $associated_summits = $main_associated_summits;
            }
            $this->associated_summits = $associated_summits;
            
            if (count($route_ids))
            {
                $associated_route_outings = array_filter($associated_childs, array('c2cTools', 'is_outing'));
                if (count($associated_route_outings))
                {
                    $associated_outings = array_filter($this->associated_docs, array('c2cTools', 'is_outing'));
                    if (count($associated_outings))
                    {
                        $outing_ids = array();
                        foreach ($associated_outings as $outing)
                        {
                            $outing_ids[] = $outing['id'];
                        }
                        foreach ($associated_route_outings as $outing)
                        {
                            if (!in_array($outing['id'], $outing_ids))
                            {
                                $associated_outings[] = $outing;
                            }
                        }
                    }
                    else
                    {
                        $associated_outings = $associated_route_outings;
                    }
                }
            }
            
            array_unshift($route_ids, $current_doc_id);
            $this->ids = implode('-', $route_ids);
            
            $this->associated_huts = c2cTools::sortArray(array_filter($this->associated_docs, array('c2cTools', 'is_hut')), 'elevation');
            
            if (count($associated_parkings))
            {
                $associated_parkings = Association::addChild($associated_parkings, array_filter($associated_childs, array('c2cTools', 'is_parking')), 'pp');
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
            $associated_outings = Outing::fetchAdditionalFields($associated_outings, true);
            $this->nb_outings = count($associated_outings);
            // sort outings
            usort($associated_outings, array('c2cTools', 'cmpDate'));
            // group them by blocks
            $outings_limit = sfConfig::get('app_users_outings_limit');
            $a = array();
            $i = 0;
            while (count($associated_outings) - $i*$outings_limit > $outings_limit)
            {
                $a[] = array_slice($associated_outings, $i * $outings_limit, $outings_limit);
                $i++;
            }
            $a[] = array_slice($associated_outings, $i * $outings_limit);
            $this->associated_outings = $a;
    
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

    protected function getHighestSummitName()
    {
        $id = $this->getRequestParameter('id');
        if (empty($id)) return null;
        $user = $this->getUser();
        $prefered_cultures = $user->getCulturesForDocuments();
        $associated_docs = Association::findAllWithBestName($id, $prefered_cultures);
        $associated_summits = c2cTools::sortArrayByName(array_filter($associated_docs, array('c2cTools', 'is_summit')));
        // extract highest associated summit, and prepend its name to display this route's name.
        $highest_summit_name = c2cTools::extractHighestName($associated_summits);

        return $highest_summit_name;
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

                // clear view cache of associated summit ...
                $this->clearCache('summits', $summit_id, false, 'view');
            }        

            // here if we have created a new document and if $this->document->get('geom_wkt') is null, then use associated doc geom associations:
            // this allows us to filter on ranges even if no GPX is uploaded
            if ($this->new_document && $summit_id && !$this->document->get('geom_wkt'))
            {
                // get all associated regions (3+maps) with this summit:
                $associations = GeoAssociation::findAllAssociations($summit_id, array('dr', 'dc', 'dd', 'dm'));
                // replicate them with route_id instead of summit_id:
                foreach ($associations as $ea)
                {
                    $a = new GeoAssociation();
                    $a->doSaveWithValues($id, $ea->get('linked_id'), $ea->get('type'));
                }
            }

            parent::endEdit(); // redirect to document view
        }
        elseif ($this->link_with = $this->getRequestParameter('link')) 
        {
            // form viewing => get linked doc
            $linked_doc = Document::find('Summit', $this->link_with, array('id', 'module'));
            
            if ($linked_doc)
            {
                $linked_doc->setBestCulture($this->getUser()->getCulturesForDocuments());
                $this->linked_doc = $linked_doc;
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
     * This function is used to get summit specific query paramaters. It is used
     * from the generic action class (in the documents module).
     */
    protected function getQueryParams() {
        $where_array  = array();
        $where_params = array();
        if ($this->hasRequestParameter('min_min_elevation'))
        {
            $min_min_elevation = $this->getRequestParameter('min_min_elevation');
            if (!empty($min_min_elevation)) {
                $where_array[]  = 'routes.min_elevation >= ?';
                $where_params[] = $min_min_elevation;
            }
        }
        if ($this->hasRequestParameter('max_min_elevation'))
        {
            $max_min_elevation = $this->getRequestParameter('max_min_elevation');
            if (!empty($max_min_elevation)) {
                $where_array[]  = 'routes.min_elevation <= ?';
                $where_params[] = $max_min_elevation;
            }
        }
        if ($this->hasRequestParameter('min_max_elevation'))
        {
            $min_max_elevation = $this->getRequestParameter('min_max_elevation');
            if (!empty($min_max_elevation)) {
                $where_array[]  = 'routes.max_elevation >= ?';
                $where_params[] = $min_max_elevation;
            }
        }
        if ($this->hasRequestParameter('max_max_elevation'))
        {
            $max_max_elevation = $this->getRequestParameter('max_max_elevation');
            if (!empty($max_max_elevation)) {
                $where_array[]  = 'routes.max_elevation <= ?';
                $where_params[] = $max_max_elevation;
            }
        }
        if ($this->hasRequestParameter('min_height_diff_up'))
        {
            $min_height_diff_up = $this->getRequestParameter('min_height_diff_up');
            if (!empty($min_height_diff_up)) {
                $where_array[]  = 'routes.height_diff_up >= ?';
                $where_params[] = $min_height_diff_up;
            }
        }
        if ($this->hasRequestParameter('max_height_diff_up'))
        {
            $max_height_diff_up = $this->getRequestParameter('max_height_diff_up');
            if (!empty($max_height_diff_up)) {
                $where_array[]  = 'routes.height_diff_up <= ?';
                $where_params[] = $max_height_diff_up;
            }
        }
        if ($this->hasRequestParameter('min_duration'))
        {
            $min_duration = $this->getRequestParameter('min_duration');
            if (!empty($min_duration)) {
                $where_array[]  = 'routes.duration >= ?';
                $where_params[] = $min_duration;
            }
        }
        if ($this->hasRequestParameter('max_duration'))
        {
            $max_duration = $this->getRequestParameter('max_duration');
            if (!empty($max_duration)) {
                $where_array[]  = 'routes.duration <= ?';
                $where_params[] = $max_duration;
            }
        }
        if ($this->hasRequestParameter('activities'))
        {
            $activities = $this->getRequestParameter('activities');
            $where = $this->getWhereClause(
                $activities, 'app_activities_list', '? = ANY (routes.activities)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('global_rating'))
        {
            $global_ratings = $this->getRequestParameter('global_rating');
            $where = $this->getWhereClause(
                $global_ratings, 'mod_routes_global_ratings_list', 'routes.global_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('route_type'))
        {
            $route_types = $this->getRequestParameter('route_type');
            $where = $this->getWhereClause(
                $route_types, 'mod_routes_route_types_list', 'routes.route_type = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('min_height_diff_down'))
        {
            $min_height_diff_down = $this->getRequestParameter('min_height_diff_down');
            if (!empty($min_height_diff_down)) {
                $where_array[]  = 'routes.height_diff_down >= ?';
                $where_params[] = $min_height_diff_down;
            }
        }
        if ($this->hasRequestParameter('max_height_diff_down'))
        {
            $max_height_diff_down = $this->getRequestParameter('max_height_diff_down');
            if (!empty($max_height_diff_down)) {
                $where_array[]  = 'routes.height_diff_down <= ?';
                $where_params[] = $max_height_diff_down;
            }
        }
        if ($this->hasRequestParameter('min_route_length'))
        {
            $min_route_length = $this->getRequestParameter('min_route_length');
            if (!empty($min_route_length)) {
                $where_array[]  = 'routes.route_length >= ?';
                $where_params[] = $min_route_length;
            }
        }
        if ($this->hasRequestParameter('max_route_length'))
        {
            $max_route_length = $this->getRequestParameter('max_route_length');
            if (!empty($max_route_length)) {
                $where_array[]  = 'routes.route_length <= ?';
                $where_params[] = $max_route_length;
            }
        }
        if ($this->hasRequestParameter('min_difficulties_height'))
        {
            $min_difficulties_height = $this->getRequestParameter('min_difficulties_height');
            if (!empty($min_difficulties_height)) {
                $where_array[]  = 'routes.difficulties_height >= ?';
                $where_params[] = $min_difficulties_height;
            }
        }
        if ($this->hasRequestParameter('max_difficulties_height'))
        {
            $max_difficulties_height = $this->getRequestParameter('max_difficulties_height');
            if (!empty($max_difficulties_height)) {
                $where_array[]  = 'routes.difficulties_height <= ?';
                $where_params[] = $max_difficulties_height;
            }
        }
        if ($this->hasRequestParameter('is_on_glacier'))
        {
            $is_on_glacier = $this->getRequestParameter('is_on_glacier');
            if (!empty($is_on_glacier)) {
                $falsetrue = $is_on_glacier == 0 ? 'false' : 'true';
                $where_array[]  = 'routes.is_on_glacier = ' . $falsetrue;
            }
        }
        if ($this->hasRequestParameter('facing'))
        {
            $facings = $this->getRequestParameter('facing');
            $where = $this->getWhereClause(
                $facings, 'mod_routes_facings_list', 'routes.facing = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('configuration'))
        {
            $configurations = $this->getRequestParameter('configuration');
            $where = $this->getWhereClause(
                $configurations, 'mod_routes_configurations_list', '? = ANY (routes.configuration)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('engagement_rating'))
        {
            $engagement_ratings = $this->getRequestParameter('engagement_rating');
            $where = $this->getWhereClause(
                $engagement_ratings, 'mod_routes_engagement_ratings_list', 'routes.engagement_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('equipment_rating'))
        {
            $equipment_ratings = $this->getRequestParameter('equipment_rating');
            $where = $this->getWhereClause(
                $equipment_ratings, 'app_equipment_ratings_list', 'routes.equipment_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('sub_activities'))
        {
            $sub_activities = $this->getRequestParameter('sub_activities');
            $where = $this->getWhereClause(
                $activities, 'mod_routes_sub_activities_list', '? = ANY (routes.sub_activities)');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('toponeige_exposition_rating'))
        {
            $toponeige_exposition_ratings = $this->getRequestParameter('toponeige_exposition_rating');
            $where = $this->getWhereClause(
                $toponeige_exposition_ratings, 'mod_routes_toponeige_exposition_ratings_list', 'routes.toponeige_exposition_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('labande_ski_rating'))
        {
            $labande_ski_ratings = $this->getRequestParameter('labande_ski_rating');
            $where = $this->getWhereClause(
                $labande_ski_ratings, 'mod_routes_labande_ski_ratings_list', 'routes.labande_ski_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('ice_rating'))
        {
            $ice_ratings = $this->getRequestParameter('ice_rating');
            $where = $this->getWhereClause(
                $ice_ratings, 'mod_routes_ice_ratings_list', 'routes.ice_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('mixed_rating'))
        {
            $mixed_ratings = $this->getRequestParameter('mixed_rating');
            $where = $this->getWhereClause(
                $mixed_ratings, 'mod_routes_mixed_ratings_list', 'routes.mixed_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('rock_free_rating'))
        {
            $rock_free_ratings = $this->getRequestParameter('rock_free_rating');
            $where = $this->getWhereClause(
                $rock_free_ratings, 'mod_routes_rock_free_ratings_list', 'routes.rock_free_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('aid_rating'))
        {
            $aid_ratings = $this->getRequestParameter('aid_rating');
            $where = $this->getWhereClause(
                $aid_ratings, 'mod_routes_aid_ratings_list', 'routes.aid_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        if ($this->hasRequestParameter('hiking_rating'))
        {
            $hiking_ratings = $this->getRequestParameter('hiking_rating');
            $where = $this->getWhereClause(
                $hiking_ratings, 'mod_routes_hiking_ratings_list', 'routes.hiking_rating = ?');
            if (!is_null($where))
            {
                $where_array[] = $where['where_string'];
                $tmp = array_merge($where_params, $where['where_params']);
                $where_params = $tmp;
            }
        }
        $params = array(
            'select' => array(
                'routes.min_elevation',
                'routes.max_elevation',
                'routes.height_diff_up',
                'routes.duration',
                'routes.activities',
                'routes.global_rating',
                'routes.route_type',
                'routes.height_diff_down',
                'routes.route_length',
                'routes.difficulties_height',
                'routes.is_on_glacier',
                'routes.facing',
                'routes.configuration',
                'routes.engagement_rating',
                'routes.equipment_rating',
                'routes.sub_activities',
                'routes.toponeige_exposition_rating',
                'routes.labande_ski_rating',
                'routes.ice_rating',
                'routes.mixed_rating',
                'routes.rock_free_rating',
                'routes.aid_rating',
                'routes.hiking_rating'
            ),
            'where'  => array(
                'where_array'  => $where_array,
                'where_params' => $where_params
            )
        );
        return $params; 
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
    
        $fields = array('activities', 'facing', 'height_diff_up', 'global_rating', 'engagement_rating',
                        'toponeige_technical_rating', 'toponeige_exposition_rating', 'labande_ski_rating',
                        'labande_global_rating', 'rock_free_rating', 'ice_rating', 'mixed_rating', 
                        'aid_rating', 'hiking_rating');
         
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

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'rnam': return 'snamei.name';
            case 'act':  return 'm.activities';
            case 'anam': return 'ai.name';
            case 'maxa': return 'm.max_elevation';
            case 'fac':  return 'm.facing';
            case 'hdif': return 'm.height_diff_up';
            case 'time': return 'm.duration';
            case 'ralt': return 'm.elevation';
            case 'dhei': return 'm.difficulties_height';
            case 'grat': return 'm.global_rating';
            case 'erat': return 'm.engagement_rating';
            case 'prat': return 'm.equipment_rating';
            case 'frat': return 'm.rock_free_rating';
            case 'arat': return 'm.aid_rating';
            case 'irat': return 'm.ice_rating';
            case 'mrat': return 'm.mixed_rating';
            case 'trat': return 'm.toponeige_technical_rating';
            case 'expo': return 'm.toponeige_exposition_rating';
            case 'lrat': return 'm.labande_global_rating';
            case 'srat': return 'm.labande_ski_rating';
            case 'hrat': return 'm.hiking_rating';
            case 'rlen': return 'm.route_length';
            case 'geom': return 'm.geom_wkt';
            case 'lat': return 'sname.lat';
            case 'lon': return 'sname.lon';
            default: return NULL;
        }
    }

    protected function getListCriteria()
    {   
        $params_list = c2cTools::getAllRequestParameters();
        
        return Route::buildListCriteria($params_list);
    } 

    protected function filterSearchParameters()
    {
        $out = array();

        $this->addListParam($out, 'areas');

        $this->addNameParam($out, 'snam');
        $this->addCompareParam($out, 'salt');
        $this->addParam($out, 'styp');

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
        $this->addListParam($out, 'sub');
        $this->addListParam($out, 'conf');
        $this->addFacingParam($out, 'fac');
        $this->addListParam($out, 'rtyp');
        $this->addCompareParam($out, 'time');
        $this->addCompareParam($out, 'trat');
        $this->addCompareParam($out, 'expo');
        $this->addCompareParam($out, 'lrat');
        $this->addCompareParam($out, 'srat');
        $this->addCompareParam($out, 'grat');
        $this->addCompareParam($out, 'erat');
        $this->addCompareParam($out, 'prat');
        $this->addCompareParam($out, 'irat');
        $this->addCompareParam($out, 'mrat');
        $this->addCompareParam($out, 'frat');
        $this->addCompareParam($out, 'rrat');
        $this->addCompareParam($out, 'arat');
        $this->addCompareParam($out, 'hrat');
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

        $routes = $this->pager->getResults('array');

        // if they are criterias on the summit (snam, srnam, salt, styp)
        // we might have only some of the associated summits and not the 'best one' (ticket #337)
        // so we must add a new request to get the summits, display the best one and add a note to explain that the
        // other summit is associated
        // FIXME would be nice to put all in a single request (before), but I didn't manage to do it
        if ($this->hasRequestParameter('snam') || $this->hasRequestParameter('srnam') ||
            $this->hasRequestParameter('salt') || $this->hasRequestParameter('styp'))
        {
           // $routes = Route::addBestSummitName($routes, '');
        }

        Parking::addAssociatedParkings($routes, 'pr'); // add associated parkings infos to $routes
        Document::countAssociatedDocuments($routes, 'ro', true); // number of associated outings
        $this->items = Language::parseListItems($routes, 'Route');
    }
}
