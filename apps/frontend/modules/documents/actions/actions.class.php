<?php
/**
 * Generic documents module actions.
 *
 * @package    c2corg
 * @subpackage documentss
 * @version    $Id: actions.class.php 2542 2007-12-21 19:07:08Z alex $
 */
class documentsActions extends c2cActions
{
    const TOOLTIPMAXSTRLEN = 28;
    const QUERYLIMIT = 50;

    /**
     * Model class name.
     */
    protected $model_class = 'Document';
    
    /**
     * Nb of dimensions for geom column
     */   
    protected $geom_dims = 3; 
    // by default, all documents are 3D (X, Y, Z)
    // exceptions are : 
    //      - users, areas, maps : 2D (X, Y)
    //      - outings : 4D (X, Y, Z, T in traces)
    
    public static $current_version;

    /**
     * If the current version number is not set, it looks in DB
     * else it takes it from the static property current_version.
     * TODO: compare it with a document_id, because current_version is only independent from same document
     */
    public static function getCurrentVersionNb($id, $lang)
    {
    	if(!isset(self::$current_version))
        {
            self::$current_version = Document::getCurrentVersionNumberFromIdAndCulture($id, $lang);
        }

        return self::$current_version;
    }

    /**
     * This method constructs an SQL where clause, it is a helper
     * method used by subclasses.
     * $params is a request parameter array
     * $config is a configuration item identifier
     * $where is the SQL string to be used between each OR
     */
    protected function getWhereClause($params, $config, $where) {
        if (!is_array($params) || in_array(0, $params))
        {
            return null;
        }
        $where_string = null;
        $where_params = array();
        $config_list = sfConfig::get($config);
        foreach($params as $param)
        {
            foreach($config_list as $num => $str)
            {
                if ($num == $param)
                {
                    $param_num = $num;
                    break;
                }
            }
            if (isset($param_num))
            {
                if (empty($where_string))
                {
                    $where_string = $where;
                }
                else
                {
                    $where_string .= ' OR ' . $where;
                }
                $where_params[] = $param_num;
            }
        }
        if (is_null($where_string))
        {
            return null;
        }
        return array(
            'where_params'  => $where_params,
            'where_string'  => '(' . $where_string . ')'
        );
    }

    /**
     * This method takes a set DB query results and returns a subset of this
     * set based on the user's prefered language. If there are multiple
     * results for a given document, the result associated with the user's
     * prefered language is chosen. This method assumes that (a) the
     * results contain the fields 'id' and 'culture' and (b) the results
     * are ordered by id.
     */
    // FIXME: this is redundant with getTheBestLanguage($results, $model);
    protected function buildQueryResults($results_in)
    {
        // get the user's prefered languages
        // FIXME: this causes an extra DB request! (maybe no more true)
        $user_prefered_langs = $this->getUser()->getCulturesForDocuments();

        $results_out = array();
        $old_id = 0; $i = 0;
        foreach ($results_in as $result)
        {
            if ($result["id"] <> $old_id)
            {
                // always true at the beginning since sequences begin with 1
                $results_out[$i++] = $result;
                $old_id = $result["id"];
                $tmparray = array_keys($user_prefered_langs, $result["culture"]);
                $ref_culture_rank = array_shift($tmparray);
            }
            else
            {
                // change result if there's a prefered language
                $tmparray = array_keys($user_prefered_langs, $result["culture"]);
                $rank = array_shift($tmparray);
                if ($rank < $ref_culture_rank)
                {
                    $results_out[$i - 1] = $result;
                    $ref_culture_rank = $rank;
                }
            }
        }

        return $results_out;
    }
    
    /**
     * Helper method for getByXY and getById. This method builds an HTML list
     * item string associated with the information passed in the method args.
     */
    protected function getHTMLTooltipItem($id, $name, $module_name, $elevation, $document = null, $extra_info = null)
    {
        // build the string corresponding to the extra information
        $extra_str = '';
        if (!is_null($document) &&
            !is_null($extra_info) && 
            ($count = count($extra_info)) > 0)
        {
            $extra_str .= ' (';
            foreach($extra_info as $info)
            {
                $extra_str .= $document[$info['field']];
                if (isset($info['unit']))
                {
                    $extra_str .= ' ' . $this->__($info['unit']);
                }
                if (--$count > 0)
                {
                    $extra_str .= ', ';
                }
            }
            $extra_str .= ')';
        }
        
        // cut the name if the string is too long
        $diff =  (strlen($name) + strlen($elevation) + strlen($extra_str) + 4) - self::TOOLTIPMAXSTRLEN;
        if ($diff > 0 && ($start = strlen($name) - $diff) > 0)
        {
            $cut_name = substr_replace($name, '...', $start);
        }
        else
        {
            $cut_name = $name;
        }

        // build the HTML string

        $html  = '<li>';
        // picto
        $html .= '<img src="/static/images/modules/' . $module_name . '_mini.png"/> ';
        // name (hypertext link)
        $html .= link_to($cut_name, $module_name . '/view?id=' . $id, array('title' => $name));
        // elevation
        $html .= (($module_name != 'users') && ($module_name != 'outings') && ($module_name != 'routes')) ? ', ' . $elevation . ' ' . $this->__('meters') : '';
        // specific information
        $html .= $extra_str;
        return $html;
    }

    /**
     * Return document information associated with given x,y coordinates.
     */
    protected function queryByXY()
    {
        $x = $this->getRequestParameter('x');
        $y = $this->getRequestParameter('y');
        $width = $this->getRequestParameter('width');
        $height = $this->getRequestParameter('height');
        $bbox = $this->getRequestParameter('bbox');
        
        $query_params = array();
        $where_array  = array();
        $select_array = array();

        if ($this->hasRequestParameter('layers'))
        {
            $layers = $this->getRequestParameter('layers');
            $layer_array = explode(",", $layers);
        }
        else if ($this->getModuleName() != 'documents')
        {
            $layer_array = array($this->getModuleName());
        }

        // Build the WHERE clause
        if (isset($layer_array))
        {
            $tmp = array();
            foreach ($layer_array as $layer)
            {
                $tmp[] = 'module = ?';
                $query_params[] = $layer;

            }
            $where_array[] = '(' . implode(' OR ', $tmp) . ')';
        }
        $query = gisQuery::getQueryByXY($x, $y, $width, $height, $bbox);
        $where_array[] = $query['where_string'];
        $tmp = array_merge($query_params, $query['where_params']);
        $query_params = $tmp;
        
        $table = $this->getModuleName();
        $table_i18n = $table . '_i18n';

        $select_array = array(
            $table_i18n . '.id',
            $table_i18n . '.culture',
            $table_i18n . '.name',
            $table      . '.module',
            $table      . '.elevation'
        );
        
        if (count($select_array) <= 0 ||
            count($where_array)  <= 0)
        {
            // invalid query
            return;
        }
        
        $select = implode(',', $select_array);
        $where  = implode(' AND ', $where_array);
                  
        $sql = "SELECT
                  " . $select . "
                FROM
                  " . $table_i18n . " LEFT JOIN " . $table . " USING(id)
                WHERE
                  $where
                ORDER BY
                  " . $table_i18n . ".id
                LIMIT 6";

        // fetch the results from the DB
        $query_results = sfDoctrine::connection()->standaloneQuery($sql, $query_params)->fetchAll();
        
        // if the result includes routes, add best summit name to their names.
        if (in_array('routes', $layer_array))
        {
            // filter routes out of results array
            $routes = array_filter($query_results, array('c2cTools', 'is_route'));
            if (!empty($routes))
            {
                // add best summit name
                $routes = Route::addBestSummitName($routes);
                // merge both results arrays
                $query_results = array_filter($query_results, array('c2cTools', 'is_not_route'));
                $query_results = array_merge($query_results, $routes);
            }
        }

        // build the actual results based on the user's prefered language
        $results = $this->buildQueryResults($query_results);
        //  FIXME: use getTheBestLanguage($results, $model) ?

        if (count($results) <= 0)
        {
            return array('html' => '');
        }
        else if (count($results) > 5)
        {
            return array('html' => '<div class="tooltip">' . $this->__('Too many results') . '</div>');
        }
        
        // build two arrays for later use:
        // module_table associates a list of document ids to each module name
        // id_table associates a module name and a document name to each document id
        $module_table = array();
        $id_table = array();
        foreach($results as $result)
        {
            $id = $result['id'];
            $name = $result['name'];
            $module_name = $result['module'];
            $elevation = $result['elevation'];

            if (!array_key_exists($module_name, $module_table))
            {
                $module_table[$module_name] = array();   
            }
            $module_table[$module_name][] = $id;

            $id_table[$id] = array(
                'name'      => $name,
                'module'    => $module_name,
                'elevation' => $elevation
            );
        }

        // below we bypass the view layer for faster response, to be able to use
        // link_to(), which is commonly used from within templates, we need to
        // explicitely load Tag and Url helpers.
        sfLoader::loadHelpers(array('Tag', 'Url'));

        // build one HTML table per module
        $html  = '<div class="tooltip"><ul>';
        foreach ($module_table as $module_name => $ids)
        {
            // get fields to display from config
            $extra_info = sfConfig::get('app_tooltips_' . $module_name);

            if (!is_null($extra_info) && count($extra_info) > 0)
            {
                // get fields to select from $extra_info
                $fields = null;
                foreach ($extra_info as $info)
                {
                    if (is_null($fields))
                    {
                        $fields = array($info['field']);
                    }
                    else
                    {
                        $fields[] = $info['field'];
                    }
                }

                // get model class name from module name
                $model = c2cTools::module2model($module_name);

                // get documents associated with ids
                $documents = Document::findIn($model, $ids, $fields);

                foreach ($documents as $document)
                {
                    $id = $document['id'];
                    $name = $id_table[$id]['name'];
                    $module_name = $id_table[$id]['module'];
                    $elevation = $id_table[$id]['elevation'];
                    $html .= $this->getHTMLTooltipItem($id, $name, $module_name, $elevation, $document, $extra_info);
                }
            }
            else
            {
                foreach ($id_table as $id => $value)
                {
                    $name = $value['name'];
                    $module_name = $value['module'];
                    $elevation = $value['elevation'];
                    $html .= $this->getHTMLTooltipItem($id, $name, $module_name, $elevation);
                }
            }
        }
        $html .= '</ul></div>';

        return array('html' => $html);
    }

    /**
     * Return document information associated with a given document id.
     */
    protected function queryById()
    {
        // below we bypass the view layer for faster response, to be able to use
        // link_to(), which is commonly used from within templates, we need to
        // explicitely load Tag and Url helpers.
        sfLoader::loadHelpers(array('Tag', 'Url', 'Pagination'));
        //
        $id = $this->getRequestParameter('id');

        // Build the SQL request. For buildQueryResults() (see below) to be able
        // to do its job: the results of the SQL query must contain the
        // document's id and culture.
        $query_results = Doctrine_Query::create()->
             select('i.id, i.culture, i.name, d.module, d.elevation')->
             from('Document d, d.DocumentI18n i')->
             where('i.id = ?', $id)->
             execute(array(), Doctrine::FETCH_ARRAY);

        // build the actual results based on the user's prefered language
        $results = getTheBestLanguage($query_results, 'Document');
        
        if (count($results) <= 0)
        {
            return array("html" => '');
        }
        
        $html  = '<div class="tooltip"><ul>';
        foreach($results as $id => $result) {
            $name = $result['DocumentI18n'][0]['name'];
            $module_name = $result['module'];
            $elevation = $result['elevation'];
    
            // get fields to display from config
            $extra_info = sfConfig::get('app_tooltips_' . $module_name);

            if (!is_null($extra_info) && count($extra_info) > 0)
            {
                // get fields to select from $extra_info
                $fields = null;
                foreach($extra_info as $info)
                {
                    if (is_null($fields))
                    {
                        $fields = array($info['field']);
                    }
                    else
                    {
                        $fields[] = $info['field'];
                    }
                }

                // get model class name from module name
                $model = c2cTools::module2model($module_name);

                // get associated document with id
                $document = Document::find($model, $id, $fields);
            
                $html .= $this->getHTMLTooltipItem($id, $name, $module_name, $elevation, $document, $extra_info);
            }
            else
            {
                $html .= $this->getHTMLTooltipItem($id, $name, $module_name, $elevation);
            }
        }
        $html .= '</ul></div>';

        return array("html" => $html);
    }

    protected function doQuery()
    {
        // queries always have request parameter "name"
        if (!$this->hasRequestParameter('name'))
        {
            return;
        }
            
        $query_params = array();
        $where_array  = array();
        $select_array = array();

        // create WHERE statement associated with the name
        $name = $this->getRequestParameter('name');
        if (!empty($name))
        {
            $where_array[] = 'search_name LIKE remove_accents(?)';
            $query_params[] = "%$name%";
        }

        // create WHERE statement associated with the bbox
        if ($this->hasRequestParameter('bbox'))
        {
            $bbox = $this->getRequestParameter('bbox');
            if (!empty($bbox))
            {
                $query = gisQuery::getQueryByBbox($bbox);
                $where_array[] = $query['where_string'];
                $tmp = array_merge($query_params, $query['where_params']);
                $query_params = $tmp;
            }
        }

        $table = $this->getModuleName();
        $table_i18n = $table . '_i18n';

        $select_array = array(
            $table_i18n . '.id',
            $table_i18n . '.culture',
            $table_i18n . '.name',
            $table      . '.module',
            $table      . '.geom_wkt'
        );

        $params = $this->getQueryParams();
        if (count($params['select']) > 0)
        {
            $tmp = array_merge($select_array, $params['select']);
            $select_array = $tmp;
        }
        if (!empty($params['where']['where_array']))
        {
            $tmp = array_merge($where_array, $params['where']['where_array']);
            $where_array = $tmp;
        }
        if (count($params['where']['where_params']) > 0)
        {
            $tmp = array_merge($query_params, $params['where']['where_params']);
            $query_params = $tmp;
        }
        
        $select = implode(',', $select_array);

        // Build the SQL request. For buildQueryResults() (see below) to be able
        // to do its job: (a) the results of the SQL query must contain the
        $sql_common = " FROM $table_i18n LEFT JOIN $table USING(id) ";
        if (count($where_array) > 0)
        {
            $where = implode(' AND ', $where_array);
            $sql_common .= " WHERE $where ";
        }

        $sql_count = "SELECT count(*) $sql_common";
        $sql = "SELECT $select $sql_common ORDER BY $table_i18n.id LIMIT " . self::QUERYLIMIT;

        // get the number of results
        $num_results = sfDoctrine::connection()->standaloneQuery($sql_count, $query_params)->fetchColumn(0);

        // fetch the results from the DB
        $query_results = sfDoctrine::connection()->standaloneQuery($sql, $query_params)->fetchAll();
        
        // We add here the best summit name to included routes.
        if ($table == 'documents')
        {
            $routes = array_filter($query_results, array('c2cTools', 'is_route'));
            $query_results = array_filter($query_results, array('c2cTools', 'is_not_route'));
        }
        else if ($table == 'routes')
        {
            $routes = $query_results;
            $query_results = array();
        }
        if (isset($routes) && !empty($routes))
        {
            // add best summit name
            $routes = Route::addBestSummitName($routes);
            $query_results = array_merge($query_results, $routes);
        }
            
        // build the actual results based on the user's prefered language
        $results = $this->buildQueryResults($query_results);
    
        $html = $geo = '';

        if ($num_results == 0)
        {
            $html = '<p>' . ucfirst($this->__('no results')) . '</p>';
        }
        else
        {
            if ($num_results > self::QUERYLIMIT)
            {
                $html  = '<p>';
                $html .= $this->__('%1% results, %2% displayed, restrict your search', array(
                    "%1%" => $num_results, "%2%" => count($results)));
                $html .= '</p>';
            }
            $html .= '<table class="list"><tbody>';
            $geo_objects = array();
            $table_list_even_odd = 0;
            foreach ($results as $result)
            {
                $table_class = ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd';
                $html .= '<tr class="' . $table_class . '">';
                $html .= $this->getFormattedResult($result);
                $html .= '<td>';
                if (!isset($result['geom_wkt']) || empty($result['geom_wkt']))
                {
                    $html .= $this->__('Document is not geolocated');
                }
                else
                {
                    $html .= link_to_function($this->__('Localize on map'), 'highlight_object(' . $result['id'] . ')');
                }
                $html .= '</td></tr>';
                $geo_objects[] = $result['id'] . ':' . $result['geom_wkt'];
            }
            $html .= '</tbody></table>';
    
            if (count($geo_objects) > 0)
            {
                $geo = implode(';', $geo_objects);
            }
        }

        return array('html' => $html, 'geo' => $geo);
    }

    /**
     * Retrieves given document data.
     * With is joined information: metadata, i18n
     * @param integer document id
     * @param string culture
     * @param integer version id
     * @return Document-like object
     */
    protected function getDocument($id, $lang, $version = NULL)
    {
        if (!is_null($version))
        {
            // get the current document version (version that's used by default when viewing a document)
            $this->current_version = self::getCurrentVersionNb($id, $lang);

            // if document version to get is already the newest one and
            // if we are simply viewing it, redirect to standard view URL.
            $this->redirectIf($this->current_version <= $version && $this->getActionName() == 'view',
                              '@document_by_id_lang?module=' . $this->getModuleName() . "&id=$id&lang=$lang");
            // FIXME : this method is not exclusively used for document viewing, thus should not include a View action redirection ?

            // an old version is requested: we look in archives tables
            $old_version = $this->getArchiveData($id, $lang, $version);

            // culture-dependent data
            $i18n_data = $this->getArchiveI18nData($old_version);

            // versioning metadata
            $metadatas = $this->getMetaData($old_version);

            // we create a new object document with both culture-dependent and -independent data
            if (!$document = Document::createFromArchive($this->model_class, $old_version,
                                                         $i18n_data, $metadatas, $version))
            {
                $this->setNotFoundAndRedirect();
            }
            $document->setAvailable();
        }
        else
        {
        	// it's a standard document, we find it in model_class
            if (!$document = Document::find($this->model_class, $id))
            {
                $this->setNotFoundAndRedirect();
            }
            $this->setDefaultNameIfEmpty($document);
        }

        return $document;
    }

    protected function setDefaultNameIfEmpty($document)
    {
        if (!($document->get('name')))
        {
            // Document does not have a name in the current culture yet.
            // So we guess the best name to use
            // according to user language preferences.
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $document->setBestName($prefered_cultures);
            $document->setNotAvailable();
        }
        else
        {
            $document->setAvailable();
        }
    }

    protected function getArchiveData($id, $lang = null, $version)
    {
        if (!$document = Document::getArchiveData($this->model_class . 'Archive',
                                                  $this->model_class . 'I18nArchive',
                                                  $id, $lang, $version))
        {
            $this->setErrorAndRedirect('Requested version not found', '@homepage');
        }

        return $document;
    }

    protected function getArchiveI18nData($version)
    {
        $document = $version->get('document_version')->get($this->model_class . 'I18nArchive');
        return $document;
    }

    protected function getMetaData($version)
    {
        return $version->get('document_version')->get('history_metadata');
    }

    public function executePreview()
    {
        $this->concurrent_edition = false;

        // preview of document as entered by current user.
        $document = new $this->model_class;
        $document->setPreview();
        //$document->setCulture($this->getRequestParameter('lang')); // not useful ?
        $this->document = $document;
        $this->setDataFields($this->document);
        $this->setTemplate('../../documents/templates/preview');
    }


    public function executeViewCurrent()
    {
        $this->concurrent_edition = true;

        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');

        $document = $this->getDocument($id, $lang);
        $document->setPreview();
        $document->setCulture($lang);
        $this->document = $document;

        $this->setTemplate('../../documents/templates/preview');
    }

    /**
     * Executes the actions to display the Home Page
     */
    public function executeHome()
    {
        // user filters:
        if (c2cPersonalization::isMainFilterSwitchOn())
        {
            $langs = c2cPersonalization::getLanguagesFilter();
            $ranges = c2cPersonalization::getPlacesFilter();
            $activities = c2cPersonalization::getActivitiesFilter();
        }
        else
        {
            $langs = array();
            $ranges = array();
            $activities = array();
        }
        
        // some of the latest documents published on the site
        $this->latest_outings = Outing::listLatest(sfConfig::get('app_recent_documents_outings_limit'),
                                                   $langs, $ranges, $activities);
        $this->latest_articles = Article::listLatest(sfConfig::get('app_recent_documents_articles_limit'),
                                                     $langs, $activities); 
        $this->latest_images = Image::listLatest(sfConfig::get('app_recent_documents_images_limit'),
                                                 $activities); 
        
        // outings from metaengine:
        $region_ids = c2cTools::convertC2cRangeIdsToMetaIds($ranges); 
        $activity_ids = c2cTools::convertC2cActivityIdsToMetaIds($activities);
        $metaengine_url = sfConfig::get('app_meta_engine_base_url') . 
                          'outings?system_id=2,3,4' . 
                          '&orderby=outing_date' . 
                          '&outing_lang=' . implode(',', $langs) . 
                          '&activity_ids=' . implode(',', $activity_ids) .
                          '&region_id=' . implode(',', $region_ids);
        
        try
        {
            $feed = sfFeedPeer::createFromWeb($metaengine_url);
            $this->meta_items = sfFeedPeer::aggregate(array($feed),
                                                      array('limit' => sfConfig::get('app_recent_documents_metaengine_limit')))
                                          ->getItems();
        }
        catch (Exception $e)
        {
            // for instance if metaengine is down.
            $this->meta_items = array();
        }

        // forum latest active threads
        $this->latest_threads = PunbbTopics::listLatest(sfConfig::get('app_recent_documents_threads_limit'), $langs);
        
        // Custom welcome message:
        $prefered_langs = $this->getUser()->getCulturesForDocuments();
        $this->message = Message::find($prefered_langs[0]);

        $this->figures = sfConfig::get('app_figures_list');

        $this->getResponse()->addMeta('robots', 'index, follow');
    }


    public function executeIndex()
    {
        $this->redirect('@default_index?module=' . $this->getModuleName()); 
    }

    /**
     * Executes view action.
     */
    public function executeView()
    {
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        $version = $this->getRequestParameter('version');

        $user = $this->getUser();
        $prefered_cultures = $user->getCulturesForDocuments();
        $module = $this->getModuleName();
        
        // we check here if document id requested corresponds to $module model
        // and if not, redirect to true module...
        if ($this->model_class == 'Document') // then we are not in a daughter class (should be a rare case)
        {
            $doc = Document::find('Document', $id, array('module')); 
            $this->redirect('@document_by_id_lang?module='.$doc->get('module')."&id=$id&lang=$lang"); 
        }

        if (is_null($lang))
        {
            // if lang isn't set, we use the prefered language session and redirect to the good URL
            // (for caching reasons, this cannot be silent)
            if (!$lang = DocumentI18n::findBestCulture($id, $prefered_cultures, $this->model_class)) 
            {
                $this->setNotFoundAndRedirect();
            }
            else
            {
                $this->redirect("@document_by_id_lang?module=$module&id=$id&lang=$lang");
            }
        }

        $document = $this->getDocument($id, $lang, $version); 
        // no need to test whether document has been found :
        // already done in getDocument method.

        if ($to_id = $document->get('redirects_to'))
        {
            $this->setWarning('Current document has been merged into document %1%',
                            array('%1%' => $to_id), false);
        }

        $title = $this->__($module) .' :: '. $document->get('name');

        if($document->isArchive())
        {
            $this->getResponse()->addMeta('robots', 'noindex, nofollow');
            $this->metadata = $document->getMetadatas();
            $title .= ' :: ' . $this->__('revision') . ' ' . $version ;
            $this->associated_docs = array();
        }
        else
        {
            $this->getResponse()->addMeta('robots', 'index, follow');
            $this->metadata = NULL;
            $this->current_version = NULL;
            
            // display associated docs:
            $this->associated_docs = Association::findAllWithBestName($id, $prefered_cultures);
            $this->associated_articles = array_filter($this->associated_docs, array('c2cTools', 'is_article'));
            $this->associated_sites = array_filter($this->associated_docs, array('c2cTools', 'is_site'));
            $this->associated_books = array_filter($this->associated_docs, array('c2cTools', 'is_book'));
            $this->associated_images = Document::fetchAdditionalFieldsFor(
                                            array_filter($this->associated_docs, array('c2cTools', 'is_image')), 
                                            'Image', 
                                            array('filename'));
            // display geo associated docs:
            $geo_associated_docs = GeoAssociation::findAllWithBestName($id, $prefered_cultures);
            $this->associated_areas = array_filter($geo_associated_docs, array('c2cTools', 'is_area'));
            $this->associated_maps = array_filter($geo_associated_docs, array('c2cTools', 'is_map'));
        }

        $this->setPageTitle($title);

        $this->document = $document;
        $this->languages = $document->getLanguages();
    }

    public function executeGeoportail()
    {
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        
        $this->document = $this->getDocument($id, $lang); 
        // no need to test whether document has been found :
        // already done in getDocument method.

        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $this->associated_docs = Association::findAllWithBestName($id, $prefered_cultures);

        $this->associated_images = Document::fetchAdditionalFieldsFor( 
                                       array_filter($this->associated_docs, array('c2cTools', 'is_image')),  
                                       'Image', array('filename'));

        // deactivate automatic inclusion of JS and CSS files 
        $response = $this->context->getResponse();
        $response->setParameter('javascripts_included', true, 'symfony/view/asset');
        $response->setParameter('stylesheets_included', true, 'symfony/view/asset');
    }

    /**
     * Get the history of a document
     * TODO: paginate
     */
    public function executeHistory()
    {
        $id = $this->getRequestParameter('id');

        if (!Document::checkExistence($this->model_class, $id))
        {
            $this->setNotFoundAndRedirect();
        }

        $lang = $this->getRequestParameter('lang');
        if (is_null($lang))
        {
            // if culture isn't set, we use the current interface language culture
            // and redirect to the good URL
            $lang = $this->getUser()->getCulture();
            $this->redirect('@document_history?module=' . $this->getModuleName() . "&id=$id&lang=$lang"); 
        }


        $this->current_version = self::getCurrentVersionNb($id, $lang);

        if ($this->current_version > 0)
        {
            $documents = Document::getHistoryFromIdAndCulture($id, $lang);
        }
        else
        {
            $this->setErrorAndRedirect('The requested document does not exist in this language',
                                       '@default_index?module=' . $this->getModuleName());
        }

        $document = $documents[0];

        // set some template's variables
        $this->document = $document;
        $this->versions = $documents;
        $this->exists_in_lang = 1;
        $this->document_name = $document['i18narchive']['name'];

        // set template and title
        $this->setTemplate('../../documents/templates/history');
        $this->setPageTitle($this->document_name . ' ' . $this->__('history'));
        $this->getResponse()->addMeta('robots', 'noindex, nofollow');
    }

    /**
     * Gets document comments.
     */
    public function executeComment()
    {
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');

        // one cannot comment a document which does not exist. 
        if (!$document = DocumentI18n::findName($id, $lang, $this->model_class))
        {
            $this->setNotFoundAndRedirect();
        }
        
        // redirect to true document module if $model_class == Document
        if ($this->model_class == 'Document')
        {
            $document = Document::find('Document', $id, array('module'));
            $this->redirect('@document_comment?module=' . $document->get('module') . "&id=$id&lang=$lang"); 
        }

        $this->document_name = $document->get('name');
        $this->comments =  PunbbComm::GetComments($id.'_'.$lang);
        $this->exists_in_lang = 1;
        $this->setTemplate('../../documents/templates/comment');
        $this->setPageTitle($this->__('Comments') . ' :: ' . $this->document_name );
        $this->getResponse()->addMeta('robots', 'index, follow');
    }

    /**
     * Display differences between given document versions.
     */
    public function executeDiff()
    {
        $id          = $this->getRequestParameter('id');
        $old_version = $this->getRequestParameter('old');
        $new_version = $this->getRequestParameter('new');
        $lang        = $this->getRequestParameter('lang');

        if ($this->getContext()->getRequest()->getMethod() == sfRequest::POST)
        {
            $this->redirect('@document_diff?module=' . $this->getModuleName() . 
                            "&id=$id&lang=$lang&new=$new_version&old=$old_version");
        }

        // Diff displaying is performed in view using Diff helper
        $this->old_document = $this->getDocument($id, $lang, $old_version);
        $this->new_document = $this->getDocument($id, $lang, $new_version);

        $this->old_metadata = $this->old_document->getMetadatas();
        $this->new_metadata = $this->new_document->getMetadatas();

        $this->current_version = self::getCurrentVersionNb($id, $lang);

        $this->fields = Document::getVisibleFieldNamesByObject($this->old_document);

        $this->setTemplate('../../documents/templates/diff');
        $this->setPageTitle($this->new_document->get('name') . ' :: ' . $this->__('diff') . ' ' .
                            $old_version . ' > ' . $new_version );
        $this->getResponse()->addMeta('robots', 'noindex, nofollow');
    }

    /**
     * Executes list action.
     */
    public function executeList()
    {
        $this->pager = call_user_func(array($this->model_class, 'browse'),
                                      $this->getListSortCriteria(),
                                      $this->getListCriteria());
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();

        $this->setPageTitle($this->__($this->getModuleName() . ' list'));
        $this->setTemplate('../../documents/templates/list');
    }

    /**
     * Get list of criteria used to filter items list.
     * Must be overridden in every module.
     * @return array
     */
    protected function getListCriteria()
    {
        $criteria = array();
        
        if (($name = $this->getRequestParameter('name')) && !empty($name))
        {
            $criteria['name'] = $name;
        }

        return $criteria;
    }

    /**
     * Detects list sort parameters: what field to order on, direction and 
     * number of items per page (npp).
     * @return array
     */
    protected function getListSortCriteria()
    {
        $orderby = $this->getRequestParameter('orderby', NULL);
        return array('order_by' => $this->getSortField($orderby),
                     'order'    => $this->getRequestParameter('order', 
                                                              sfConfig::get('app_list_default_order')),
                     'npp'      => $this->getRequestParameter('npp',
                                                              sfConfig::get('app_list_maxline_number'))
                     );
    }

    protected function getSortField($orderby)
    {
        switch ($orderby)
        {
            case 'name': return 'mi.search_name';
            default: return NULL;
        }
    }

    protected function JSONResponse($results)
    {
        $json_service = new Services_JSON();
        return $this->renderText($json_service->encode($results));
    }

    public function executeQuery()
    {
        if ($this->hasRequestParameter('id'))
        {
            $results = $this->queryById();
        }
        else if ($this->hasRequestParameter('x') &&
                 $this->hasRequestParameter('y') &&
                 $this->hasRequestParameter('width') &&
                 $this->hasRequestParameter('height') &&
                 $this->hasRequestParameter('bbox'))
        {
            $results = $this->queryByXY();
        }

        if (isset($results))
        {
            // we've got our response, send it
            return $this->JSONResponse($results);
        }

        $results = $this->doQuery();
        if (!is_null($results)) {
            return $this->JSONResponse($results);
        }

        /* the view layer will do the job */
        $this->setTemplate('../../documents/templates/query');
    }
    
    protected function getAreas($area_type)
    {
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $areas = Area::getRegions($area_type, $prefered_cultures); 
        // $ranges = array('1' => 'vercors', '2' => 'bauges');
        
        if (($area_type == 1) && ($prefered_ranges = c2cPersonalization::getPlacesFilter()) && !empty($prefered_ranges))
        {
            // extract from $ranges the ranges whose key match the values of $prefered_ranges array:
            $prefered_ranges_assoc = array();
            foreach ($prefered_ranges as $i => $id)
            {
                $prefered_ranges_assoc[$id] = $areas[$id];
            }            
            // substract from this list those from personalization filter
            $areas = array_diff($areas, $prefered_ranges_assoc);
            // order alphabetically ranges from personalization filter
            asort($prefered_ranges_assoc);
            // add them at the top of the list and keep keys
            $areas = $prefered_ranges_assoc + array(0 => '-------') + $areas;
        }
        return $areas;
    }

    public function executeFilter()
    {
        $ranges = $this->getAreas(1);
        $this->ranges = $ranges;
        $this->setTemplate('../../documents/templates/filter');
    }
    
    public function executeFilterredirect()
    {
        $route = '/' . $this->getModuleName() . '/list'; 
        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $criteria = array_merge($this->filterSearchParameters(),
                                    $this->filterSortParameters());
            if ($criteria)
            {
                $route .= '?' . implode('&', $criteria);
            }
        }
        c2cTools::log("redirecting to $route");
        $this->redirect($route);
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant search parameters.
     * Might need to be overridden within module actions class.
     * @return array
     */
    protected function filterSearchParameters()
    {
        return array();
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant sort parameters.
     * @return array
     */
    protected function filterSortParameters()
    {
        $sort = array();

        if (($npp = $this->getRequestParameter('npp')) && 
             $npp != sfConfig::get('app_list_maxline_number'))
        {
            $sort[] = "npp=$npp";
        }

        $this->addParam($sort, 'orderby');
        $this->addParam($sort, 'order');

        return $sort;
    }

    /**
     * filter for people who have the right to edit current document (linked people for outings, original editor for articles ....)
     * to be overridden in children classes.
     */
    protected function filterAuthorizedPeople($id)
    {
    }
    
    /**
     * filter edits which must require additional parameters (link for instance : outing with route)
     * to be overridden in children classes.
     */
    protected function filterAdditionalParameters()
    {
    }

    /**
     * Check what we are doing in edit action (creating or editing)
     * and populate objects depending
     *
     * @return void
     */
    protected function setEditFormInformation()
    {
        if ($id = $this->getRequestParameter('id')) // update an existing document
        {
            if (!$document = Document::find($this->model_class, $id))
            {
                $this->setNotFoundAndRedirect();
            }
                        
            $this->document = $document;
            
            // here, filter people who have the right to edit a particular document (eg: personal outing, article or such document when current user is linked).
            $this->filterAuthorizedPeople($id);

            if ($version = $this->getRequestParameter('version'))
            {
                // Editing an archive (reversion to a previous version)

                $lang = $this->getRequestParameter('lang');
                $is_protected = $document->get('is_protected');
                // see documentsActions::getDocument()
                $old_version = $this->getArchiveData($id, $lang, $version);
                $i18n_data = $this->getArchiveI18nData($old_version);
                $metadatas = $this->getMetaData($old_version);

                $document = Document::createFromArchive($document, $old_version,
                                                      $i18n_data, $metadatas, $version);
                // no need to check if document exists : already done in getArchiveData

                // if current document is protected, all previous versions are not editable:
                if ($is_protected)
                {
                    $document->set('is_protected', true);
                }
                else // to prevent double warning
                {
                    $this->editing_archive = true;
                }
            }
            else
            {
                $culture = $document->getCulture();
                // we join version Nb here, so that it is accessible in object (useful for edition lock)
                $version = Document::getCurrentVersionNumberFromIdAndCulture($id, $culture);
                $document->setVersion($version);

                // we set a default name for the document (useful for languages in which it does not exist)
                $this->setDefaultNameIfEmpty($document);
            }
            
            c2cTools::log("doc actions :: setEditFormInformation with protect status :". (int)$document->get('is_protected'));

            if ($document->get('is_protected') == true)
            {
                $referer = $this->getRequest()->getReferer();
                $this->setErrorAndRedirect('You cannot edit a protected document', $referer);
            }

            $this->new_document = false;

            $this->setPageTitle($this->__('Edition of "%1%"', array('%1%' => $document->getName())));
        }
        else
        {
            // here, filter edits which must require additional parameters (link for instance : outing with route)
            $this->filterAdditionalParameters();
            
            // create a new document
            $document = new $this->model_class;
            $this->document = $document;
            
            // we populate here some fields, for instance if we are creating a new outing, already associated with a route.
            $this->populateCustomFields();
            
            $this->new_document = true;
            $this->setPageTitle($this->__('Creating new ' . $this->getModuleName()));
        }
    }
    
    /**
     * populates custom fields (for instance if we are creating a new outing, already associated with a route)
     * to be overriden in daugther classes.
     */
    protected function populateCustomFields()
    {
    }


    public function handleErrorEdit()
    {
        $this->setEditFormInformation();

        // repopulate form after an error 
        // NB: this might also be done via fillin: enabled: true in validate/edit.yml 
        $this->document = $this->populateFromRequest($this->document);
        $this->document_name = $this->document->get('name');
        
        $linked_doc_id = $this->getRequestParameter('summit_id', 0) + $this->getRequestParameter('document_id', 0);
        if ($linked_doc_id > 0)
        {
            $linked_doc = Document::find('Document', $linked_doc_id, array('id', 'module'));
            if ($linked_doc)
            {
                $linked_doc->setBestCulture($this->getUser()->getCulturesForDocuments());
                $this->linked_doc = $linked_doc;
            }
            else
            {
                $this->setNotFoundAndRedirect();
            }
        }
        
        $this->setTemplate('../../documents/templates/edit');
        return sfView::SUCCESS;
    }

    /**
     * Executes edit action.
     */
    public function executeEdit()
    {
        // populate objects for form display depending on what we are doing (creating, editing)
        $this->setEditFormInformation();

        $document = $this->document;
        $module_name = $this->getModuleName();
        if($module_name != 'users')
        {
            $this->document_name = $document->get('name');
        }
        else
        {
            $this->document_name = $document->get('private_data')->getSelectedName();
        }

        // Culture (lang) is automatically defined in Hydrate,
        // redefined in the model.
        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $lang = $this->getRequestParameter('lang');

            $user_id = $this->getUser()->getId();
            $is_minor = $this->getRequestParameter('rev_is_minor', false);
            $message = $this->getRequestParameter('rev_comment');

            $document->setCulture($lang);

            $old_lon = $document->get('lon');
            $old_lat = $document->get('lat');

            $this->setDataFields($document);
        
            // upload potential GPX file to server and set WKT field.
            $request = $this->getRequest();
            
            if ($request->hasFiles() && $request->getFileName('file'))
            {
                c2cTools::log('request has files');
                // it is necessary to preserve both tests nested.
                if ($wkt = $this->getWktFromFileUpload($request))
                {
                    c2cTools::log('wkt extracted');
                    $document->set('geom_wkt', $wkt);

                    // NB: these fields exist in both objects for which a file upload is possible (outings, routes)
                    $_a = ParseGeo::getCumulatedHeightDiffFromWkt($wkt);
                    if (!$document->get('height_diff_up'))
                    {
                        $document->set('height_diff_up', $_a['up']);
                        c2cTools::log('height diff up set from wkt : ' . $_a['up']);                        
                    }
                    if (!$document->get('height_diff_down'))
                    {
                        $document->set('height_diff_down', $_a['down']);
                        c2cTools::log('height diff down set from wkt : ' . $_a['down']);                        
                    }
                    $message = '[geodata] ' . ((!$message) ? "Edit with geometry upload" : $message);
                }
            }
            
            $message = (!$message) ? "Edit in $lang" : $message;
                        
            // if document has a geometry, compute and create associations with ranges, depts, countries.
            // nb: association is performed upon document creation with initial geometry
            // OR when the centroid (lon, lat) has moved during an update. 
            
            $needs_geom_association = isset($wkt) ||  // gpx or kml file has been uploaded 
                                      ($document->get('lon') != $old_lon && $document->get('lon') != null) || 
                                      ($document->get('lat') != $old_lat && $document->get('lat') != null); // geom centroid has moved

            // we prevent here concurrent edition :

            // fake data so that second test always fails on summit creation (and when document is an archive) :
            $rev_when_edition_begun = 1;
            $current_rev = 0;

            // test if id exists (summit update) before checking concurrent edition
            // and if this is not an archive (editing an old document to reverse wrong changes)
            // (because only useful for document update) :
                        
            if (($id = $this->getRequestParameter('id')) && (!$this->getRequestParameter('editing_archive')))
            {
                $rev_when_edition_begun = $this->getRequestParameter('revision');
                $current_rev = $document->getVersion();
            }

            c2cTools::log("Document $id in $lang : rev when edition begun : $rev_when_edition_begun - current rev : $current_rev");

            if ($rev_when_edition_begun < $current_rev)
            {
                c2cTools::log("Document $id in $lang has been concurrently saved. $rev_when_edition_begun < $current_rev");
                // document has been saved by someone else during edition
                // we present datas entered in the same form
                $this->document = $document;
                // and we launch a preview with the document in its true current state:
                $this->concurrent_edition = true;
                // if the current_document variable is available in the edit template, we display the preview of it.
            }
            else
            {
                $document->doSaveWithMetadata($user_id, $is_minor, $message);
                $this->success = true; // means that child class can redirect to document view after other operations if needed (eg: associations).
                $this->document = $document;
                $id = $document->get('id');
                
                if ($needs_geom_association)
                {
                    c2cTools::log('executeEdit: needs_geom_association');
                    // we create new associations :
                    //  (and delete old associations before creating the new ones)
                    //  (and do not create outings-maps associations)
                    $nb_created = gisQuery::createGeoAssociations($id, true, ($document->get('module') != 'outings')); 
                    c2cTools::log("created $nb_created geo associations");
                    
                    // if summit or site coordinates have moved (hence $needs_geom_association = true), 
                    // refresh geo-associations of associated routes (from summits) and outings (from routes or sites)
                    $this->refreshGeoAssociations($id);
                }
                
                // we clear views, histories, diffs in every language (content+interface):
                $this->clearCache($module_name, $id);
            }
        }
        
        // All modules will use the same template
        $this->setTemplate('../../documents/templates/edit');
        $this->endEdit();
    }
    
    /**
     * Overridden in child classes 
     * this is because we sometimes have to do things when centroid coordinates have moved.
     */
    protected function refreshGeoAssociations($id)
    {    
        // do nothing by default
    }
    
    /**
     * Overridden in child classes 
     * this is because we sometimes have to do things like doc associations in specific modules before we redirect.
     */
    protected function endEdit()
    {    
        if (($this->getRequest()->getMethod() == sfRequest::POST) && !$this->concurrent_edition)
        {
            $this->redirect('@document_by_id_lang?module=' . $this->getModuleName() .
                            '&id=' . $this->document->get('id') .
                            '&lang=' . $this->document->getCulture()); 
        }
    }
    
    /**
     * Returns a 3D or 4D WKT 
     */
    protected function getWktFromFileUpload($request)
    {
        $fileName = $request->getFileName('file');
        
        // FIXME: $fileSize is always 0 : Symfony bug ?
        $fileSize = $request->getFileSize($fileName);
        c2cTools::log("Uploaded file size: $fileSize");
        if ($fileSize > sfConfig::get('app_traces_maxfilesize'))
        {
            // FIXME : feedback info
            return false;
        }
                           
        if ($request->hasFileError($fileName))
        {
            // FIXME : feedback info
            return false;
        }
            
        $path = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . c2cTools::generateUniqueName(); 
        $status = $request->moveFile('file', $path);
        $type = c2cTools::getFileType($path); 
        c2cTools::log("File $fileName uploaded to: $path with status: $status and a file type of: $type");
        
        $finalPath = $path;
        $wkt = NULL;
        // we rename file according to true type
        if ($type)
        {
            $status = rename($path, "$path.$type");
            if ($status)
            {
                $finalPath = $path . '.' . $type;
                c2cTools::log("File renamed: $finalPath");
                $wkt = $this->getWktFromFile($finalPath, $type);
                c2cTools::log("wkt computed");
            }
        }
        
        // we clear temp file :
        unlink($finalPath);
        
        //c2cTools::log("getWktFromFileUpload has generated this WKT: $wkt");
        return $wkt;
    }
    
    /*
     * getWktFromFile returns a 3D or 4D WKT 
     * it calls methods from class ParseGeo to extract it from the file
     *
     */
    protected function getWktFromFile($path, $type)
    {
        switch ($type)
        {
            // TODO later: handle KML and KMZ files
            case "gpx":
                c2cTools::log('getWktFromFile: calls ParseGeo::gpx2wkt with dims=' . $this->geom_dims);
                ParseGeo::filterGpx($path);
                $wkt = ParseGeo::gpx2wkt($path, $this->geom_dims);
                break;
            /* 
            // KML is no more accepted because code is buggy
            case "kml":
                c2cTools::log('getWktFromFile: calls ParseGeo::kml2wkt with dims=' . $this->geom_dims);
                $wkt = ParseGeo::kml2wkt($path, $this->geom_dims);
                // KML is accepted for outings (4D) but then, time will not be taken into account (zero padded)
                // the reason is that it is too difficult to parse.
                break;
            */
            default: 
                return false;
        }
        return $wkt;
    }

    protected function setDataFields($document)
    {
        foreach (Document::getVisibleFieldNamesByModel($this->model_class) as $field_name)
        {
            $field_value = $this->getRequestParameter($field_name);
            $document->set($field_name, $field_value);
        }
    }

    public function executeWhatsnew()
    {
        $user_id = $this->getRequestParameter('user', null);
        $lang = $this->getRequestParameter('lang', null);
    
        $this->pager = Document::listRecentChangesPager($this->model_class, $user_id, $lang);
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();

        $this->setTemplate('../../documents/templates/whatsnew');
        $this->setPageTitle($this->__('Recent changes'));
    }
    
    public function executeLatestassociations()
    {
        $this->pager = AssociationLog::listRecentChangesPager();
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();

        $this->setTemplate('../../documents/templates/latestassociations');
        $this->setPageTitle($this->__('Recent associations'));
    }

    public function executeSearch()
    {
        if ($query_string = $this->getRequestParameter('q'))
        {
            if (($module = $this->getRequestParameter('type')) && 
                in_array($module, sfConfig::get('app_modules_list')))
            {
                $model = c2cTools::module2model($module);
            }
            else
            {
                $model = 'Document';
                $module = NULL;
            }

            // search
            $this->pager = Document::getListByName($query_string, $model);
            // TODO : add best summit name to route names in these results.
            
            $this->pager->setPage($this->getRequestParameter('page', 1));
            $this->pager->init();

            $nb_results = $this->pager->getNbResults();

            // no needs of a list for one document
            if ($nb_results == 1)
            {
                // if only one document matches, redirect automatically towards it
                $results = $this->pager->getResults('array');
                
                $item = Language::getTheBest($results, $this->model_class); // FIXME: is it really necessary here ?
                $item = array_shift($item);
                
                $this->redirect('@document_by_id_lang?module=' . $item['module'] . 
                                                                '&id=' . $item['id'] . 
                                                                '&lang=' . $item[$model . 'I18n'][0]['culture']);
            }

            // redirect to classic list if in an official module
            if ($nb_results > 1 && !empty($module))
            {
                $this->redirect(sprintf('%s/list?name=%s', $module, urlencode($query_string)));
            }
            
            $this->model_i18n = $model . 'I18n';
            $this->setPageTitle($this->__($this->getModuleName() . ' search'));
        }
        else
        {
            $this->forward404('need a string');
        }
    }
    
    protected function deleteLinkedFile($id)
    {
        // nothing to do here, but in imagesActions class.
    }

    public function executeDelete()
    {
        $referer = $this->getRequest()->getReferer();

        if ($id = $this->getRequestParameter('id'))
        {
            $document = Document::find($this->model_class, 
                                        $id, 
                                        array('module'));
            if (!$document) 
            {
                $this->setErrorAndRedirect('Document does not exist', $referer);
            }
                                        
            $module = $document->get('module');
        
            $this->deleteLinkedFile($id);
            
            $nb_dv_deleted = Document::doDelete($id);
            c2cTools::log("executeDelete: deleted document $id and its $nb_dv_deleted versions (all langs taken into account)");

            if ($nb_dv_deleted)
            {
                // cache clearing for deleted doc in every lang, without whatsnew:
                $this->clearCache($module, $id, false);
                
                // find all associated docs
                $associated_docs = Association::findAllAssociatedDocs($id, array('id', 'module'));
                foreach ($associated_docs as $doc)
                {
                    // clear their view cache
                    $this->clearCache($doc['module'], $doc['id'], false, 'view');
                }
                // remove all general associations concerned by this document
                $deleted = Association::deleteAll($id);
                c2cTools::log("executeDelete: deleted $deleted general associations concerning this doc");
                
                // find all geo associated docs
                $associated_docs = GeoAssociation::findAllAssociatedDocs($id, array('id', 'module'));
                foreach ($associated_docs as $doc)
                {
                    // clear their view cache
                    $this->clearCache($doc['module'], $doc['id'], false, 'view');
                }
                // remove all geo associations concerned by this document
                $deleted += GeoAssociation::deleteAll($id);

                c2cTools::log("executeDelete: deleted $deleted general associations concerning this doc");
                // flash info:
                $this->setNoticeAndRedirect('Document deleted', "@default_index?module=$module");
            }
            else
            {
                $this->setErrorAndRedirect('Document could not be deleted', $referer);
            }
        }
        else
        {
            $this->setErrorAndRedirect('Could not understand your request', $referer);
        }
    }

    public function executeProtect()
    {
        $referer = $this->getRequest()->getReferer();

        if ($id = $this->getRequestParameter('id'))
        {
            $document = Document::find($this->model_class, 
                                        $id, 
                                        array('redirects_to', 'is_protected'));

            if (!$document) 
            {
                $this->setErrorAndRedirect('Document does not exist', $referer);
            }

            // if current document has been merged into another one, it cannot be deprotected
            if ($document->get('redirects_to'))
            {
                $msg = 'Current document has been merged into another one, thus cannot be deprotected';
                return $this->setErrorAndRedirect($msg, $referer);
            }

            $user_id = $this->getUser()->getId();

            $doc_status = $document->get('is_protected');
            $message = 'Document has been ';
            $message .= ($doc_status) ? 'deprotected' : 'protected';

            $document->set('is_protected', !$doc_status);
            $document->doSaveWithMetadata($user_id, true, $message);

            // cache clearing for current doc in every lang:
            $this->clearCache($this->getModuleName(), $id);
            // set flash info:
            return $this->setNoticeAndRedirect($message, $referer);
        }
        else
        {
            return $this->setErrorAndRedirect('Could not understand your request', $referer);
        }
    }

    public function executeMerge()
    {
        $referer = $this->getRequest()->getReferer();
        $module = $this->getModuleName();
        $from_id = $this->getRequestParameter('from_id');
        $to_id = $this->getRequestParameter('document_id');
        
        
        $user_id = $this->getUser()->getId();
        
        if (!($from_id))
        {
            $this->setErrorAndRedirect('Could not understand your request', $referer);
        }
        
        if ($from_id == $to_id)
        {
            $this->setErrorAndRedirect('Could not perform association with itself', $referer);
        }
            
        // test whether the document_from is protected
        $document_from = Document::find($this->model_class, $from_id, array('is_protected', 'module')); 
        if (!$document_from) 
        {
            $this->setErrorAndRedirect('Document does not exist', $referer);
        }

        if ($document_from->get('is_protected')) // TODO: factorize
        {
            $this->setErrorAndRedirect('Current document is write-protected', $referer);
        }
        
        if ($to_id)
        {
            // test whether the document_to is protected
            $document_to = Document::find($this->model_class, $to_id, array('is_protected', 'module')); 
            if (!$document_to) 
            {
                $this->setErrorAndRedirect('Document does not exist', $referer);
            }
        
            if ($document_to->get('is_protected')) // TODO: factorize
            {
                return $this->setErrorAndRedirect('Target document is write-protected', $referer);
                // FIXME: ajax feedback ?
            }

            if ($document_from->get('module') != $document_to->get('module'))
            {
                return $this->setErrorAndRedirect('Document types differ, thus cannot be merged', $referer);
                // FIXME: ajax feedback ?
            }

            // if we are here, both documents are of the same type.
            $document_from = Document::find($this->model_class, $from_id, array('is_protected', 'redirects_to')); 
            // merging consists of a redirection and an edition blocking :
            $document_from->set('redirects_to', $to_id);
            $document_from->set('is_protected', true);
                
            $conn = sfDoctrine::Connection();
            try
            {
                $conn->beginTransaction();

                // it also consists in a merging of document associations with the destination document
                // thus, we have to replace all allusions of $from_id (in both columns) into $to_id in DocumentAssociation table.
                $associations = Association::findAllAssociations($from_id);
                foreach ($associations as $a)
                {
                    if ($a['main_id'] == $from_id)
                    {
                        c2cTools::log("Merging association: [$from_id, " . $a['linked_id'] . ', ' . $a['type'] . "] into [$to_id, " . $a['linked_id'] . ', ' . $a['type'] . ']');
                        // create new association (only if it does not pre-exist)
                        if (!Association::find($to_id, $a['linked_id'], $a['type']) && ($to_id != $a['linked_id']))
                        {
                            $b = new Association;
                            $b->doSaveWithValues($to_id, $a['linked_id'], $a['type'], $user_id);
                            c2cTools::log('done');
                        }
                        
                        // delete old association
                        $old = Association::find($from_id, $a['linked_id'], $a['type']);
                        
                    }
                    elseif ($a['linked_id'] == $from_id)
                    {
                        c2cTools::log('Merging association: [' . $a['main_id'] . ", $from_id, " . $a['type'] . '] into [' . $a['main_id'] . ", $to_id, " . $a['type'] . ']');
                        // create new association
                        if (!Association::find($a['main_id'], $to_id, $a['type']) && ($to_id != $a['main_id']))
                        {
                            $b = new Association;
                            $b->doSaveWithValues($a['main_id'], $to_id, $a['type'], $user_id);
                            c2cTools::log('done');
                        }
                        
                        // delete old association
                        $old = Association::find($a['main_id'], $from_id, $a['type']);
                    }
                    $old->delete();
                    // log this ?
                }
            
                // we update the document_from
                $document_from->doSaveWithMetadata($user_id, 
                                                false, 
                                                "Merged with document [[$module/$to_id|$to_id]]");

                $conn->commit();
            
            }
            catch (Exception $e)
            {
                $conn->rollback();
                throw $e;
            }

            // cache clearing:
            $this->clearCache($module, $from_id);
            $this->clearCache($module, $to_id, false, 'view'); // associations have been transfered

            $this->setNoticeAndRedirect('Document %1% has been merged into document %2%',
                                            "@document_by_id?module=$module&id=$to_id" ,
                                            array('%1%' => $from_id,
                                                  '%2%' => $to_id));
        }
            
        $this->setTemplate('../../documents/templates/merge');
    }

    /**
     * Validate comment field when editing a document
     */
    public function validateEdit()
    {
    	if($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $rev_comment = $this->getRequestParameter('rev_comment');

            if(strlen($rev_comment) > sfConfig::get('app_comment_max_lengh'))
            {
                $this->getRequest()->setError('rev_comment', $this->__('The comment field is %1% characters max' , array('%1%' => sfConfig::get('app_comment_max_lengh'))));
                return false;
            }
        }

        return true;
    }

    /**
     * Executes autocomplete action.
     * returns formated list of best matching names and their ids
     */
    public function executeAutocomplete()
    {
        $model = $this->model_class;
        $module = c2cTools::model2module($model);
        $string = $this->getRequestParameter($module . '_name'); // beginning of name string

        // useful protection :
        if (strlen($string) < sfConfig::get('app_autocomplete_min_chars')) // typically 3 or 4
        {
            return $this->renderText('<ul></ul>');
        }

        // return all documents matching $string in given module/model
        // NB: autocomplete on outings must only return those for which the current user is linked to. 
        // (because one cannot modify an outing that one is not associated with)
        $user_id = ($module == 'outings') ? $this->getUser()->getId() : 0;
        
        $results = Document::searchByName($string, $model, $user_id);
        $nb_results = count($results);

        if ($nb_results == 0)
        {
            return $this->ajax_feedback_autocomplete('no results');
        }

        if ($nb_results > sfConfig::get('app_autocomplete_max_results')) // typically 15
        {
            return $this->ajax_feedback_autocomplete('Too many results. Please go on typing...');
        }

        // build the actual results based on the user's prefered language
        $items = Language::getTheBest($results, $model);

        // if module = documents, add the object type inside brackets
        $html = '<ul>';
        foreach ($items as $item)
        {
            $identifier = ($model == 'Document') ? $this->__(substr($item['module'], 0, -1)) . ' ' : '' ;
            $postidentifier = ($model == 'Outing') ? ' (' . $item['date'] . ')' : '' ;
        	$html .= '<li id="'.$item['id'].'">'.$item[$this->model_class . 'I18n'][0]['name']." [$identifier" . $item['id'] . "]$postidentifier</li>";
        }
        $html .= '</ul>';

        // format the response and send back :
        return $this->renderText($html);
    }

    /**
     * Executes Feed action
     * NB: cannot be cached ...
     */
    public function executeFeed()
    {
        //$feed = new sfRss201Feed();
        $feed = new sfGeoRssFeed();
        $lang = $this->getRequestParameter('lang');
        $module = $this->getModuleName();
        $id = $this->getRequestParameter('id');
        $mode = $this->getRequestParameter('mode');

        switch ($mode)
        {
            case 'editions':
                $description = "Latest $module editions in $lang"; // FIXME : offer translation of these texts in $lang 
                $title = "Camptocamp.org $module feed";
                $link = "@feed?module=$module&lang=$lang";
                break;
            case 'creations':
                $description = "Latest $module creations in $lang";
                $title = "Camptocamp.org $module feed";
                $link = "@creations_feed?module=$module&lang=$lang";
                break;
            default :
                // check that document $id exists in lang $lang, and retrieve its name.
                if (!$document = DocumentI18n::findName($id, $lang, $this->model_class))
                {
                    $this->setNotFoundAndRedirect();
                }
                $name = $document->get('name');
                $description = "Latest editions for $name in $lang";
                $title = "Camptocamp.org $name feed";
                $link = "@document_feed?module=$module&id=$id&lang=$lang";
                break;
        }

        // TODO: i18n?
        $feed->setTitle($title);
        $feed->setLink($link);
        $feed->setDescription($description);
        $feed->setAuthorName('Camptocamp.org');

        $max_number = 30; // FIXME: config ?

        //usage: listRecent($model, $limit, $user_id = null, $lang = null, $doc_id = null, $mode = 'editions')
        $items = Document::listRecent($this->model_class, $max_number, null, $lang, $id, $mode);

        sfLoader::loadHelpers('SmartFormat');

        foreach ($items as $item)
        {
            $item_id = $item['document_id'];
            $new = $item['version'];
            $module_name = $item['archive']['module'];
            $name = $item['i18narchive']['name'];
            $lang = $item['culture'];
            $feedItemTitle = ($id) ? "$name - revision $new" : $name;

            $feedItem = new sfGeoFeedItem();
            $feedItem->setTitle($feedItemTitle);

            // user can display his nickname, login_name, private_name
            $user_name_to_use = $item['history_metadata']['user_private_data']['name_to_use'];

            if ($mode == 'creations')
            {
                $feedItem->setLink("$module_name/view?id=$item_id&lang=$lang");
            }
            else
            {
                $feedItem->setLink("$module_name/view?id=$item_id&lang=$lang&version=$new");
            }
            $feedItem->setAuthorName($item['history_metadata']['user_private_data'][$user_name_to_use]);
            //$feedItem->setAuthorEmail($item['history_metadata']['user_private_data']['email']);
            $feedItem->setPubdate(strtotime($item['created_at']));
            $feedItem->setUniqueId("$item_id-$lang-$new");
            $feedItem->setLongitude($item['archive']['lon']);
            $feedItem->setLatitude($item['archive']['lat']);
            $feedItem->setDescription(smart_format($item['history_metadata']['comment']));

            $feed->addItem($feedItem);
        }

        $this->feed = $feed;
        $this->setTemplate('../../documents/templates/feed');
    }

    protected function setNotFoundAndRedirect()
    {
        $this->setErrorAndRedirect($this->model_class . ' not found',
                                   '@default_index?module=' . $this->getModuleName());
    }
    
    protected function hydrateBestName($docs)
    {
        // FIXME: this function does less than its name implies : 
        // it does not really hydrate the best name: it just sets the best culture to a collection of objects
        // thus, db request are triggered, each of them fetching name + description (which might not be what we want) 
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        foreach ($docs as $doc) $doc->setBestCulture($prefered_cultures);
    }
    
    /**
     * Executes Quickcreate action.
     * if id exists, return just the name of summit, 
     * if id = 0 and name provided, quickly create summit whose name was given
     *
     * routes/quickcreate?link=summit_id // in action, specify that a link received for Route model is mandatorily a summit one.
     * plus return whole list of associated routes to this summit with created one = selected
     */
    public function executeQuickcreate()
    {
        sfLoader::loadHelpers(array('Url', 'Tag', 'Form'));

        $name = $this->getRequestParameter('name'); // name string = title of document to create
        $link_with = $this->getRequestParameter('link_with'); // eventually, a link parameter might be received
        
        $model = $this->model_class; // type of document to create
        $lcmodel = strtolower($model);
        $id_string = $lcmodel . '_id';
        // convention : 
        // if we are looking for a summit, id key is summit_id
        // if we are looking for a route, id key is route_id
        $id = $this->getRequestParameter($id_string);
                
        $user = $this->getUser();
        $lang = $user->getCulture();
        $module = $lcmodel . 's';
        
        // user must be logged
        if (!$user->isConnected())
        {
            return $this->ajax_feedback('Your session is over. Please login again.');
        }
        
        if ($id) // existing document found by autocomplete
        {
            // if given id is not null, then do not create document, just fetch it :
            $document = Document::find($model, $id, array('id'));
            //hydrate best name :
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $document->setBestCulture($prefered_cultures);
        }
        else
        {
            // here, we must create doc.
            // useful protection :
            if (strlen($name) < sfConfig::get('app_autocomplete_min_chars')) // typically 3 or 4
            {
                return $this->ajax_feedback('Could not create document with so short a title');
            }

            $document = new $model;
            $document->setCulture($lang);
            $document->set('name', $name);
            $document->doSaveWithMetadata($user->getId(), false, "Quick creation of a $lcmodel");
            
            $id = $document->get('id');
            
            // a link request only occurs when a new document has to be quickly created.
            if ($link_with)
            {
                switch ($model)
                {
                    case 'Route':
                        $summit = Document::find('Summit', $link_with, array('id')); 
                        if (!$summit)
                        {
                            return $this->ajax_feedback('Summit not found'); 
                        }
            
                        // association has to be done in SummitRoute 
                        // link route $id with summit $link_with
                        try
                        {
                            $sr = new Association();
                            $sr->set('main_id', $link_with);
                            $sr->set('linked_id', $id);
                            $sr->set('type', 'sr');
                            $sr->save();
                        }
                        catch (exception $e)
                        {
                            // fixme : catch exception.
                        }
                        break;
                    default: 
                        // for the moment, we do not manage other associations than summits with route upon quick document creations.
                        break;
                }
            }            
        }

        // format the response and send back :
        if ($link_with)
        {
            // return whole list of associated routes to this summit ($link_with) with created one = selected one
            $routes = Association::findAllWithBestName($link_with, $this->getUser()->getCulturesForDocuments(), 'sr');
        
            $output = '';
            foreach ($routes as $route)
            {
                $route_id = $route['id'];
                $output .= ($route_id == $id) ? 
                                '<option value="' . $route['id'] . '" selected="selected">' . $route['name'] . '</option>' :
                                '<option value="' . $route['id'] . '">' . $route['name'] . '</option>' ;
            }
            return $this->renderText($output);
        }
        else
        {
            return $this->renderText(
                            input_hidden_tag($id_string, $id) . 
                            link_to($document->get('name'), "@document_by_id?module=$module&id=$id"));
        }
    }

    /**
     * returns a short description of current document in best possible lang
     */
    public function executeGetdescription()
    {
        $id = $this->getRequestParameter('id');
        
        if (!$id)
        {
            return $this->ajax_feedback('Missing id parameter');
        }
        
        $user = $this->getUser();
        
        // if session is time-over
        if (!$user->getId())
        {
            return $this->ajax_feedback('Your session is over. Please login again.');
        }
        
        $user_prefered_langs = $user->getCulturesForDocuments();
        $desc = DocumentI18n::findBestDescription($id, $user_prefered_langs, $this->model_class);
        if (!$desc)
        {
            return $this->ajax_feedback('not available'); 
        }
        
        $max = sfConfig::get('app_shortdescription_max_length');
        $output = substr($desc, 0, $max);
        $output .= (strlen($desc) > $max) ? '...' : '';
        
        return $this->renderText($output);
    }
    
    /**
     * returns a GPX version of the document geometry
     */
    public function executeExportgpx()
    {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang'); 
        
        // this strange way of doing is because of cache
        // we have to redirect the requests to different actions, or else, users/fr/4.gpx and users/fr/4.kml are the same documents 
        // this is because they are identified with the same key : users/export?id=4&lang=fr&il=fr&c=0 ('format' is not taken into account)
        $this->Export($module, $id, $lang, 'gpx');
    }
    
    /**
     * returns a KML version of the document geometry
     */
    public function executeExportkml()
    {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang'); 
        
        $this->Export($module, $id, $lang, 'kml');
    }    
    
    /**
     * returns a JSON version of the document geometry
     */
    public function executeExportjson()
    {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang'); 
        
        $this->Export($module, $id, $lang, 'json');
    }    

    /**
     * returns a GPX, KML or GeoJSON version of the document geometry with some useful additional informations in best possible lang
     */
    protected function Export($module, $id, $lang, $format)
    {
        if (!$id) 
        {
            $this->setErrorAndRedirect('Could not understand your request',
                                        "@default_index?module=$module");
        }
        
        $document = Document::find('Document', $id, array('module', 'geom_wkt'));
        
        if (!$document || $document->get('module') != $module)
        {
            $this->setErrorAndRedirect('Document does not exist',
                                        "@default_index?module=$module");
        }
        
        if ($document->get('geom_wkt'))
        {
            $doc = DocumentI18n::findNameDescription($id, $lang);
            // document can exist (id) but not in required lang (id, lang)
            if ($doc) 
            {
                $this->name = $doc->get('name');
                $this->description = $doc->get('description');
            }
            else
            {
                $this->name = 'C2C::' . ucfirst(substr($module, 0, strlen($module) - 1)) . " $id";
                $this->description = "";
            }
            
            $this->points = explode(',', gisQuery::getEWKT($id, true));
            $response = $this->getResponse();
            
            switch ($format)
            {
                case 'gpx':
                    $this->setTemplate('../../documents/templates/exportgpx'); 
                    $response->setContentType('application/gpx+xml'); 
                    // FIXME: application/x-GPS-Exchange-Format or application/graphx or application/gpx+xml ?
                    // debate: http://tech.groups.yahoo.com/group/gpsxml/message/1649
                    break;
                case 'kml':
                    // TODO in exportKML template : add individual points with time coordinates so that time browsing is enabled in GE.
                    $this->setTemplate('../../documents/templates/exportkml'); 
                    $response->setContentType('application/vnd.google-earth.kml+xml');
                    /* Google Earth reads KML and KMZ files. The MIME type for KML files is application/vnd.google-earth.kml+xml
                    The MIME type for KMZ files is application/vnd.google-earth.kmz */
                    break;
                case 'json':
                    $this->setTemplate('../../documents/templates/exportjson'); 
                    $response->setContentType('application/json');
                    break;
            }
            $this->setLayout(false);
        }
        else
        {
            $this->setErrorAndRedirect('This document has no geometry',
                                        "@document_by_id_lang?module=$module&id=$id&lang=$lang");
        }
    }
    
    public function executeDeletegeom()
    {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        
        // check user is moderator: done in apps/frontend/config/security.yml
        
        if (!$id) 
        {
            $this->setErrorAndRedirect('Could not understand your request',
                                        "@default_index?module=$module");
        }
        
        // check document exists (and not protected ? or useless since only moderators can deprotect and delete geom ?)
        $document = Document::find($this->model_class, $id, array('id', 'is_protected', 'geom_wkt'));
        // NB: field to set in second time must be hydrated in object, else a second SELECT is triggered.
        
        if ($document && !$document->get('is_protected'))
        {
            $document->set('geom_wkt', null); // a trigger updates the wkb geom field (and others) in accordance. 
            $document->doSaveWithMetadata($this->getUser()->getId(), false, "Geometry has been deleted");

            // also delete geom associations with maps and areas:
            $nb_deleted = GeoAssociation::deleteAllFor($id, array('dm', 'dr', 'dd', 'dc'));
            c2cTools::log("executeDeletegeom: deleted $nb_deleted associated areas and maps with document $id");
        }
        else
        {
            $this->setErrorAndRedirect('This document is currently write-protected',
                                        "@document_by_id?module=$module&id=$id");
        }
        
        // clear cache 
        $this->clearCache($module, $id);
        
        $this->setNoticeAndRedirect('Geometry has been deleted', "@document_by_id?module=$module&id=$id");
    }    
    
   
    /**
     * Executes add or remove document association
     */
    public function executeAddRemoveAssociation()
    {
        
        $linked_id = $this->getRequestParameter('linked_id');
        $type = $this->getRequestParameter('type');
        
        $main_id = $this->getRequestParameter('main_' . $type . '_id');
        
        $mode = $this->getRequestParameter('mode'); 
        $strict = $this->getRequestParameter('strict', 1); // whether 'remove action' should be strictly restrained to main and linked or reversed. 
        
        $user = $this->getUser();
        $user_id = $user->getId(); 
                
        // if session is time-over
        if (!$user_id)
        {
            return $this->ajax_feedback('Session is over. Please login again.');
        }
        
        // association cannot be created/deleted with self.
        if ($main_id == $linked_id)
        {
            return $this->ajax_feedback('Operation not allowed');
        }
        
        // We check that this association type really exists 
        // for that, yaml is preferable over a db request, since all associations types are not allowed for quick associations
        // Allowed types for quick associations are :
        // formerly+ : sr, ro, ss, hr, pr, uo, us, so, st, hs, ii, ps, sb, rb, hb, ib, cc, sc, bc, hc, oc, rc, ic, uc
        // formerly :  sr, ro, ss, hr, pr, uo, st, to, tr, ht, tt, pt, sb, rb, hb, tb, cc, sc, bc, hc, oc, rc, tc, uc
        // now :       sr, ro, ss, hr, pr, uo, st, to, tr, ht, tt, pt, bs, br, bh, bt, cc, sc, bc, hc, oc, rc, tc, uc
        if (!in_array($type, sfConfig::get('app_associations_types'))) 
        {
            return $this->ajax_feedback('Wrong association type');
        }
        
        $models = c2cTools::Type2Models($type);
        $main_model = $models['main'];
        $linked_model = $models['linked'];
        
        if ($type == 'ro')
        {
            // in that case, output not only route name but also best summit name whose id has been passed (summit_id)
            $summit = explode(' [',$this->getRequestParameter('summits_name'));
            $summit_name = $summit[0];
        }
        
        $main = Document::find($main_model, $main_id, array('id', 'module')); 
        if (!$main)
        {
            return $this->ajax_feedback('Document does not exist');
        }

        // check that linked doc exists: 
        // FIXME : combine request with main doc by looking only in documents table and check 'module' field is correct ?
        $linked = Document::find($linked_model, $linked_id, array('id')); 
        if (!$linked)
        {
            return $this->ajax_feedback('Document does not exist');
        }        

        $output_string = '';
        $main_module = c2cTools::model2module($main_model);
        
        // check whether association has already been done or not
        $a = Association::find($main_id, $linked_id, $type, false); // false means not strict search (main and linked can be reversed)
        // 'remove' param is necessary to prevent disassociation if second try for association
        if ($a && $mode == 'remove') 
        { 
            // already done => association to delete
            // check that user is moderator:
            if (!$user->hasCredential('moderator'))
            {
                return $this->ajax_feedback('You do not have enough credentials to perform this operation');
            }

            // In case we have a summit route association or a route outing association, 
            // we must prevent the deletion of the last associated slave doc 
            // (route in case of summit and outing in the case of a route or a user).
            if ( ($type != 'sr' && $type != 'ro' && $type != 'uo' && $type != 'to') || Association::countMains($linked_id, $type) > 1)
            {
                // delete association in Database
                $conn = sfDoctrine::Connection();
                try
                {
                    $conn->beginTransaction();
                    
                    $a->delete();
                    
                    $al = new AssociationLog();
                    $al->main_id = $main_id;
                    $al->linked_id = $linked_id;
                    $al->type = $type;
                    $al->user_id = $user_id;
                    $al->is_creation = false;
                    $al->save();
            
                    $conn->commit();
                }
                catch (exception $e)
                {
                    $conn->rollback();
                    c2cTools::log("executeAddRemoveAssociation() : Association deletion + log failed ($main_id, $linked_id, $type, $user_id) - rollback");
                    return $this->ajax_feedback('Association deletion failed');
                }
            }
            else
            {
                return $this->ajax_feedback('Operation forbidden: last association');
            }
        }
        elseif (!$a && $mode == 'add')
        {
            // filter for addition of users on an article or a outing (because it gives them the right to modify it)
            if ($type == 'uo' || $type == 'uc') // user-outing or user-article
            {
                // current user must be already associated to current doc if he wishes to add people.
                $b = Association::find($user_id, $linked_id, $type);
        
                if (!$b && !$user->hasCredential('moderator'))
                {
                    return $this->ajax_feedback('You do not have the rights to link a user to this document');
                }
            }
            
            // not yet done => create association in Database
            $a = new Association();
            $status = $a->doSaveWithValues($main_id, $linked_id, $type, $user_id);
            if (!$status)
            {
                return $this->ajax_feedback('Association failed');
            }
            
            sfLoader::loadHelpers(array('AutoComplete'));

            $type_id_string = $type.'_'.$main_id; // $type prefix is needed to give a unique identifier to this list item
            
            switch ($type){
                case 'ss': // summits-summits associations are not strict, ie a summit can be on both sides of association.
                    $strict = 0;
                    break;
                case 'tt': // sites-sites associations 
                    $strict = 0;
                    break;
                case 'cc': // articles-articles associations 
                    $strict = 0;
                    break;
                default:
                    $strict = 1;
                    break;
            }
            
            $bestname = ($type == 'ro') ? $summit_name . ' : ' . $main->get('name') : $main->get('name') ;
            
            $output_string = '<div class="linked_elt" id="'.$type_id_string.'">'.link_to($bestname, "@document_by_id?module=" . $main->get('module') . "&id=$main_id");
            if ($user->hasCredential('moderator'))
                $output_string .= c2c_link_to_delete_element("documents/addRemoveAssociation?linked_id=$linked_id&main_".$type."_id=$main_id&mode=remove&type=$type&strict=$strict",
                                    "del_$type_id_string",
                                    "$type_id_string");
            $output_string .= '</div>';
        }
        else
        {
            return $this->ajax_feedback('Operation not allowed');
        }
        
        // view action cache clearing (without whatsnew), since association is not logged in app_history_metadata and associations only appear on view:
        $this->clearCache($main_module, $main_id, false, 'view');
        $this->clearCache(c2cTools::model2module($linked_model), $linked_id, false, 'view');
            
        return $this->renderText($output_string);
    } 


    /**
     * Executes getautocomplete action.
     * returns a bit of html and JS to perform autocomplete
     */
    public function executeGetautocomplete()
    {
        if ($this->hasRequestParameter('module_id'))
        {
            $module_id = $this->getRequestParameter('module_id'); // module on which to perform autocomplete

            // find corresponding module name.
            $modules = sfConfig::get('app_modules_list');
            $module_name = $modules[$module_id];
        }
        elseif ($this->hasRequestParameter('module_name'))
        {
            $module_name = $this->getRequestParameter('module_name');
        }
        else
        {
            return $this->renderText('');
        }
        

        sfLoader::loadHelpers(array('AutoComplete'));
        if ($module_name != 'routes')
        {
            $out = input_hidden_tag('document_id', '0') . c2c_auto_complete($module_name, 'document_id', '', null, ($this->getRequestParameter('button') != '0'));
            $out .= ($this->getRequestParameter('button') != '0') ? '</form>' : '';
        }
        else
        {
            $updated_failure = sfConfig::get('app_ajax_feedback_div_name_failure');
            $out = input_hidden_tag('summit_id', '0');
            $out .= __('Summit : ');
            $out .= input_auto_complete_tag('summits_name', 
                            '', // default value in text field 
                            "summits/autocomplete",                            
                            array('size' => '20'), 
                            array(  'after_update_element' => "function (inputField, selectedItem) { 
                                                                $('summit_id').value = selectedItem.id;
                                                                ". remote_function(array(
                                                                                        'update' => array(
                                                                                                        'success' => 'div_document_id', 
                                                                                                        'failure' => $updated_failure),
                                                                                        'url' => 'summits/getroutes',
                                                                                        'with' => "'summit_id=' + $('summit_id').value + '&div_id=document_id'",
                                                                                        'loading'  => "Element.show('indicator');", // does not work for an unknown reason
                                                                                        'complete' => "Element.hide('indicator');",
                                                                                        'success'  => "Element.show('associated_routes');",
                                                                                        'failure'  => "Element.show('$updated_failure');" . 
                                                    visual_effect('fade', $updated_failure, array('delay' => 2, 'duration' => 3)))) ."}",
                                    'min_chars' => sfConfig::get('app_autocomplete_min_chars'), 
                                    'indicator' => 'indicator')); 
            $out .= '<div id="associated_routes" style="display:none;">';
            $out .= '<span id="div_document_id"></span>';
            $out .= ($this->getRequestParameter('button') != '0') ? submit_tag(__('Add'), array(
                                    'style' =>  'padding-left: 20px;
                                                padding-right: 5px;
                                                background: url(/static/images/picto/plus.png) no-repeat 2px center;')) : '' ;
            $out .= '</div>';
        }
        
        return $this->renderText($out);
    }

    /**  
     * This function is used to get hut specific query paramaters.
     * To be overridden in extended class.
     */
    protected function getQueryParams() {
        $where_array  = array();
        $where_params = array();
        $params = array(
            'select' => array(
            ),
            'where'  => array(
                'where_array'  => $where_array,
                'where_params' => $where_params
            )
        );
        return $params; 
    }    

    /**  
     * This function is used to get a DB query result formatted in HTML.
     * To be overridden in extended class.
     */
    protected function getFormattedResult($result) {

        // Explicitely load helpers (required in the controller)        
        sfLoader::loadHelpers(array('Tag', 'Url', 'Javascript'));

        $html = '<td>' .
                link_to($result['name'], '@document_by_id?module=' . $this->getModuleName() . '&id=' . $result['id']) .
                '</td>';

        return $html;
    }

    protected static function makeCompareQueryString($field, $type, $value1, $value2)
    {
        switch ($type)
        {
            case '1':
                return "$field=>$value1";
            case '2':
                return "$field=<$value1";
            case '3':
                return "$field=$value1~$value2";
        }
    }
    
    protected function addCompareParam(&$out, $field)
    {
        if ($sel = $this->getRequestParameter($field . '_sel'))
        {
            $out[] = self::makeCompareQueryString($field, $sel,
                                                  $this->getRequestParameter($field),
                                                  $this->getRequestParameter($field . '2'));
        }
    }

    protected function addNameParam(&$out, $field)
    {
        if ($value = $this->getRequestParameter($field))
        {
            $out[] = $field . '=' . urlencode($value);
        }
    }

    protected function addListParam(&$out, $field)
    {
        if ($array = $this->getRequestParameter($field))
        {
            $out[] = $field . '=' . implode('-', $array);
        }
    }

    protected function addFacingParam(&$out, $field)
    {
        if ($sel = $this->getRequestParameter($field . '_sel'))
        {
            $value1 = $this->getRequestParameter($field);
            if ($sel == '~')
            {
                $value2 = $this->getRequestParameter($field . '2');
                $out[] = "$field=$value1~$value2";
            }
            else
            {
                $out[] = "$field=$value1";
            }
        }
    }

    protected function addParam(&$out, $field)
    {
        if ($value = $this->getRequestParameter($field))
        {
            $out[] = "$field=$value";
        }
    }

    protected function addDateParam(&$out, $field)
    {
        if ($sel = $this->getRequestParameter($field . '_sel'))
        {
            if ($date1 = $this->getRequestParameter($field))
            {
                $date1 = $date1['year'] . c2cTools::writeWith2Digits($date1['month']) . 
                         c2cTools::writeWith2Digits($date1['day']);
            }

            if ($date2 = $this->getRequestParameter($field . '2'))
            {
                $date2 = $date2['year'] . c2cTools::writeWith2Digits($date2['month']) . 
                         c2cTools::writeWith2Digits($date2['day']);
            }

            $out[] = self::makeCompareQueryString($field, $sel, $date1, $date2);
        }
    }
}
