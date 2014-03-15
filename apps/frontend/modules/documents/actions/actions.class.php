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

    protected $pseudo_id;
    
    protected $associated_docs;
    
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
        $this->document = $document;
        $this->setDataFields($this->document);

        // we need associated images for previsualisation (if the document already exists)
        $request = $this->getContext()->getRequest();
        $document_id = $request->getParameter('id', '');
        if (!empty($document_id))
        {
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $request = $this->getContext()->getRequest();
            $document_id = $request->getParameter('id');
            $module = $request->getParameter('module');
            $association_type = c2cTools::Module2Letter($module) . 'i';
            $this->associated_images = Document::fetchAdditionalFieldsFor(
                Association::findAllWithBestName($document_id, $prefered_cultures, $association_type),
                'Image', array('filename', 'image_type'));
            // filter image type?
            switch ($module)
            {
                case 'articles':
                    $filter_image_type = ($request->getParameter('article_type') == 1);
                    break;
                case 'images':
                    $filter_image_type = ($request->getParameter('image_type') == 1);
                    break;
                case 'outings':
                case 'users':
                    $filter_image_type = false;
                    break;
                default:
                    $filter_image_type = true;
                    break;
            }
        }
        else
        {
            $this->associated_images = null;
            $filter_image_type = null;
        }
        $this->filter_image_type = $filter_image_type;

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
        $timer = new sfTimer('executeHome');
        $mobile_version = c2cTools::mobileVersion();

        // user filters:
        $perso = c2cPersonalization::getInstance();
        if ($perso->isMainFilterSwitchOn())
        {
            $langs      = $perso->getLanguagesFilter();
            $ranges     = $perso->getPlacesFilter();
            $activities = $perso->getActivitiesFilter();
        }
        else
        {
            $langs = $ranges = $activities = array();
        }

        // some of the latest documents published on the site
        $latest_outings = Outing::listLatest($mobile_version ? sfConfig::get('app_recent_documents_outings_mobile_limit')
                                                             : sfConfig::get('app_recent_documents_outings_limit'),
                                                   $langs, $ranges, $activities);
        // choose best language for outings and regions names
        $latest_outings = Language::getTheBest($latest_outings, 'Outing');
        $this->latest_outings = Language::getTheBestForAssociatedAreas($latest_outings);

        $this->latest_articles = Article::listLatest($mobile_version ? sfConfig::get('app_recent_documents_articles_mobile_limit')
                                                                     : sfConfig::get('app_recent_documents_articles_limit'),
                                                     $langs, $activities);

        $latest_images = Image::listLatest($mobile_version ? sfConfig::get('app_recent_documents_images_mobile_limit')
                                                           : sfConfig::get('app_recent_documents_images_limit'),
                                           $langs, $ranges, $activities);
        $this->latest_images = Language::getTheBest($latest_images, 'Image');

        // outings from metaengine:
        $region_ids     = c2cTools::convertC2cRangeIdsToMetaIds($ranges); 
        $activity_ids   = c2cTools::convertC2cActivityIdsToMetaIds($activities);
        $this->meta_feed_url = sfConfig::get('app_meta_engine_base_url') .
                               'outings?system_id=2,3,4' .
                               '&orderby=outing_date' .
                               '&outing_lang=' . implode(',', $langs) .
                               '&activity_ids=' . implode(',', $activity_ids) .
                               '&region_id=' . implode(',', $region_ids);

        // forum latest active threads
        $this->latest_threads = PunbbTopics::listLatest($mobile_version ? sfConfig::get('app_recent_documents_threads_mobile_limit')
                                                                        : sfConfig::get('app_recent_documents_threads_limit'),
                                                        $langs, $activities);

        // forum 'mountain news' latest active threads
        $this->latest_mountain_news = PunbbTopics::listLatestMountainNews($mobile_version ? sfConfig::get('app_recent_documents_mountain_news_mobile_limit')
                                                                                          : sfConfig::get('app_recent_documents_mountain_news_limit'),
                                                                          $langs, $activities);

        // Custom welcome message:
        $prefered_langs = $this->getUser()->getCulturesForDocuments();
        $this->message = Message::find($prefered_langs[0]);

        if (!$mobile_version):
        // c2c news
        $this->latest_c2c_news = PunbbTopics::listLatestC2cNews(sfConfig::get('app_recent_documents_c2c_news_limit'), $langs);
        
        $this->figures = sfConfig::get('app_figures_list');
        endif; // mobile version

        $this->getResponse()->addMeta('robots', 'index, follow');
        c2cActions::statsdTiming('document.executeHome', $timer->getElapsedTime('executeHome'));
    }


    public function executeIndex()
    {
        $this->redirect('@default_index?module=' . $this->getModuleName(), 301); 
    }

    /**
     * Executes view action.
     */
    public function executeView()
    {
        $timer = new sfTimer('executeView');
        $this->timer = $timer;
        
        sfLoader::loadHelpers(array('General', 'MetaLink'));

        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        $version = $this->getRequestParameter('version');
        $slug = $this->getRequestParameter('slug');

        // and if not, redirect to true module...
        if ($this->model_class == 'Document') // then we are not in a daughter class (should be a rare case)
        {
            $doc = Document::find('Document', $id, array('module'));
            $module = $doc->get('module');
        }

        $user = $this->getUser();
        $prefered_cultures = $user->getCulturesForDocuments();
        $module = isset($module) ? $module : $this->getModuleName();
        
        // we check here if document id requested corresponds to $module model
        if (empty($lang))
        {
            // if lang isn't set, we use the prefered language session and redirect to the good URL
            // (for caching reasons, this cannot be silent)
            if (!$lang = DocumentI18n::findBestCulture($id, $prefered_cultures, $this->model_class)) 
            {
                c2cActions::statsdTiming('document.executeView.redirect', $timer->getElapsedTime('executeView'));
                $this->setNotFoundAndRedirect();
            }
            else
            {
                $document = $this->getDocument($id, $lang, $version);
                c2cActions::statsdTiming('document.executeView.redirect', $timer->getElapsedTime('executeView'));
                $this->redirectIfSlugMissing($document, $id, $lang, $module);
            }
        }

        $document = $this->getDocument($id, $lang, $version);
        // no need to test whether document has been found :
        // already done in getDocument method.

        if (empty($version) && $module != 'users' && (empty($slug) || ($module != 'routes' && $slug != get_slug($document))))
        {
            c2cActions::statsdTiming('document.executeView.redirect', $timer->getElapsedTime('executeView'));
            $this->redirectIfSlugMissing($document, $id, $lang, $module);
        }

        // case where module = documents, and lang, version or slug already given
        if ($this->model_class == 'Document') // then we are not in a daughter class (should be a rare case)
        {
            $this->redirect("@document_by_id_lang_slug?module=$module&id=$id&lang=$lang&slug=$slug", 301);
        }

        if ($to_id = $document->get('redirects_to'))
        {
            $this->setWarning('Current document has been merged into document %1%',
                            array('%1%' => $to_id), false);
        }

        $title = $document->get('name');
        if ($document->isArchive())
        {
            $this->getResponse()->addMeta('robots', 'noindex, nofollow');
            $this->metadata = $document->getMetadatas();
            $this->created_at = $this->metadata->get('written_at');
            $this->current_version = $version;
            $title .= ' :: ' . $this->__('revision') . ' ' . $version;
            $this->associated_docs = array();

            // we need associated images for displaying them
            $prefered_cultures = $this->getUser()->getCulturesForDocuments();
            $association_type = c2cTools::Module2Letter($module) . 'i';
            $this->associated_images = Document::fetchAdditionalFieldsFor(
                Association::findAllWithBestName($id, $prefered_cultures, $association_type),
                'Image', array('filename', 'image_type', 'date_time'));
        }
        else
        {
            if (isset($to_id)) // do not index merged docs, but robots can follow links
            {
                $this->getResponse()->addMeta('robots', 'noindex, follow');
            }
            else
            {
                $this->getResponse()->addMeta('robots', 'index, follow');
            }
            $this->metadata = NULL;
            
            if ($module != 'portals')
            {
                $version_infos = Document::getCurrentVersionInfosFromIdAndCulture($id, $lang);
                $this->current_version = $version_infos['version'];
                $this->created_at = $version_infos['created_at'];
            }
            
            // retrieve associated docs
            // some additional docs can be retrieved in module/executeView, like two hops summits

            // contains all documents directly linked
            $association_type = ($module == 'users') ? array('ui') : null;
            $associated_docs =  Association::findAllWithBestName($id, $prefered_cultures, $association_type);
            // mark them (useful information)
            foreach($associated_docs as &$doc)
            {
                $doc['directly_linked'] = true;
            }
            $this->associated_docs = $associated_docs;

            // all the linked articles
            $this->associated_articles = array_filter($this->associated_docs, array('c2cTools', 'is_article'));

            // linked sites
            $this->associated_sites = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_site')));

            // linked books. For summits and huts, the displayed books are the one linked to the linked routes
            if (!in_array($module, array('summits', 'huts')))
            {
                $this->associated_books = c2cTools::sortArrayByName(array_filter($this->associated_docs, array('c2cTools', 'is_book')));
            }

            // linked images
            // For sites and summits, we will dislay images linked to the summit/site AND its subsummits/subsites
            if (!in_array($module, array('summits', 'sites')))
            {
                $this->associated_images = Document::fetchAdditionalFieldsFor(
                                            array_filter($this->associated_docs, array('c2cTools', 'is_image')), 
                                            'Image', 
                                            array('filename', 'image_type', 'date_time', 'width', 'height'));
            }

            // Geo associated docs
            if (!in_array($module, array('articles', 'books')))
            {
                $associated_areas = GeoAssociation::findAreasWithBestName($id, $prefered_cultures);
                $this->associated_areas = $associated_areas;
            }
            if (!in_array($module, array('outings', 'articles', 'books', 'portals')))
            {
                $associated_maps = GeoAssociation::findMapsWithBestName($id, $prefered_cultures);
                $this->associated_maps = Map::getAssociatedMapsData($associated_maps);
            }
        }

        $this->needs_translation = ($lang == $user->getCulture()) ? false : $lang;
        $response = $this->getResponse();
        if ($this->needs_translation && !c2cTools::mobileVersion())
        {
            $response->addJavascript('/static/js/translation.js', 'last');
        }

        if (!in_array($module, array('summits', 'routes', 'sites', 'huts', 'products')))
        {
            $title .= ' :: ' . $this->__(substr($module, 0, -1));
            $this->setPageTitle($title);
        }
        $response->addMeta('description', $title);

        /* image_src fixes the image proposed eg by facebook
           - for an image, force it to be the image miniature
           - for a doc with attached images, leave facebook decide
           - for a book without attached image, force the c2c logo
        */
        if ($module != 'images' && !count($this->associated_images))
        {
            addMetaLink('image_src', sfConfig::get('app_images_default_meta'));
        }

        /* small js code to append QR code when printing page */
        if (!$document->isArchive() && !c2cTools::mobileVersion())
        {
            $response->addJavascript('/static/js/qrcode.js', 'last');
        }

        $this->document = $document;
        $this->languages = $document->getLanguages();
        c2cActions::statsdTiming('document.executeView', $timer->getElapsedTime('executeView'));
    }

    protected function redirectIfSlugMissing($document, $id, $lang, $module = null)
    {
        $module = empty($module) ? $this->getModuleName() : $module;
        $cda_config = sfConfig::get('app_portals_cda');
        if ($id == $cda_config['id'])
        {
            $redirection_type = 302;
        }
        else
        {
            $redirection_type = 301;
        }
        $this->redirect("@document_by_id_lang_slug?module=$module&id=$id&lang=$lang&slug=" . get_slug($document), $redirection_type);
    }

    public function executePopup()
    {
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        $this->raw = $this->getRequestParameter('raw', false);
        
        $this->document = $this->getDocument($id, $lang); 
        // no need to test whether document has been found :
        // already done in getDocument method.

        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $this->associated_docs = Association::findAllWithBestName($id, $prefered_cultures);

        $this->associated_images = Document::fetchAdditionalFieldsFor( 
                                       array_filter($this->associated_docs, array('c2cTools', 'is_image')),  
                                       'Image', array('filename'));

        $response = $this->context->getResponse();
        if ($this->raw)
        {
            $this->setLayout(false);
        }
        // deactivate automatic inclusion of js and css files by symfony
        $response->setParameter('javascripts_included', true, 'symfony/view/asset');
        $response->setParameter('stylesheets_included', true, 'symfony/view/asset');
        $this->setCacheControl();
    }

    /**
     * Get the history of a document
     * TODO: paginate
     */
    public function executeHistory()
    {
        $timer = new sfTimer('executeHistory');
        $id = $this->getRequestParameter('id');
        $model = $this->model_class;

        if (!Document::checkExistence($model, $id))
        {
            c2cActions::statsdTiming('document.executeHistory.redirect', $timer->getElapsedTime('executeHistory'));
            $this->setNotFoundAndRedirect();
        }

        $lang = $this->getRequestParameter('lang');
        if (is_null($lang))
        {
            // if culture isn't set, we use the current interface language culture
            // and redirect to the good URL
            $lang = $this->getUser()->getCulture();
            c2cActions::statsdTiming('document.executeHistory.redirect', $timer->getElapsedTime('executeHistory'));
            $this->redirect('@document_history?module=' . $this->getModuleName() . "&id=$id&lang=$lang"); 
        }


        $this->current_version = self::getCurrentVersionNb($id, $lang);

        if ($this->current_version > 0)
        {
            $documents = Document::getHistoryFromIdAndCulture($model, $id, $lang);
        }
        else
        {
            c2cActions::statsdTiming('document.executeHistory.redirect', $timer->getElapsedTime('executeHistory'));
            $this->setErrorAndRedirect('The requested document does not exist in this language',
                                       '@default_index?module=' . $this->getModuleName());
        }

        $document = $documents[0];

        // set some template's variables
        $this->document = $document;
        $this->versions = $documents;
        $this->exists_in_lang = 1;
        $this->document_name = $document[$model . 'I18nArchive']['name'];

        // set template and title
        $this->setTemplate('../../documents/templates/history');
        $this->setPageTitle($this->document_name . ' :: ' . $this->__('history'));
        $this->getResponse()->addMeta('robots', 'noindex, nofollow');
        c2cActions::statsdTiming('document.executeHistory', $timer->getElapsedTime('executeHistory'));
    }

    /**
     * Gets document comments.
     */
    public function executeComment()
    {
        $timer = new sfTimer('executeComment');
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');

        // one cannot comment a document which does not exist. 
        if (!$document = DocumentI18n::findName($id, $lang, $this->model_class))
        {
            c2cActions::statsdTiming('document.executeComment.redirect', $timer->getElapsedTime('executeComment'));
            $this->setNotFoundAndRedirect();
        }
        
        // redirect to true document module if $model_class == Document
        if ($this->model_class == 'Document')
        {
            $document = Document::find('Document', $id, array('module'));
            c2cActions::statsdTiming('document.executeComment.redirect', $timer->getElapsedTime('executeComment'));
            $this->redirect('@document_comment?module=' . $document->get('module') . "&id=$id&lang=$lang", 301); 
        }

        $this->document_name = $document->get('name');
        $this->search_name = $document->get('search_name');
        $this->comments =  PunbbComm::GetComments($id.'_'.$lang);
        // mark topic as read if user connected
        if ($this->getUser()->isConnected())
        {
            $row = $this->comments->getLast();
            $topic_id = $row->get('topic_id');
            $last_post_time = $row->get('posted');
            Punbb::MarkTopicAsRead($topic_id, $last_post_time);
        }
        $this->exists_in_lang = 1;
        $this->setTemplate('../../documents/templates/comment');
        $this->setPageTitle($this->document_name . ' :: ' . $this->__('Comments'));
        $this->getResponse()->addMeta('robots', 'index, follow');
        c2cActions::statsdTiming('document.executeComment', $timer->getElapsedTime('executeComment'));
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
        $module      = $this->getRequestParameter('module');

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

        // we need associated images for displaying them
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $association_type = c2cTools::Module2Letter($module) . 'i';
        $this->associated_images = Document::fetchAdditionalFieldsFor(
            Association::findAllWithBestName($id, $prefered_cultures, $association_type),
            'Image', array('filename', 'image_type'));

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
        $timer = new sfTimer('executeList');
        
        $module = $this->getModuleName();
        $model = $this->model_class;
        $criteria = $this->getListCriteria($model);
        
        // deal with layout
        $layout = $this->getRequestParameter('layout', null);
        $this->layout = $layout;
        if ($layout == 'light')
        {
            $this->setLayout('layout_light');
        }

        // deal with format 
        if (isset($this->format))
        {
            $format = $this->format;
        }
        else
        {
            $format = $this->getRequestParameter('format', 'list');
            $format = explode('-', $format);
            $this->format = $format;
        }

        $params_list = array_keys(c2cTools::getCriteriaRequestParameters());
        $orderby = $this->getRequestParameter('orderby', '');
        
        if ($module == 'outings' && empty($params_list) && in_array($orderby, array('name', 'onam', 'act')))
        {
            $redirect_uri = $module;
            
            $format = $this->getRequestParameter('format', '');
            if (!empty($format) && $format != 'list')
            {
                $redirect_uri .= '/format/' . $format;
            }
            
            $this->redirect($redirect_uri);
        }
        
        $this->show_images = in_array('img', $format);
        $this->setPageTitle($this->__($module . ' list'));
        if (in_array('full', $format))
        {
            $default_npp = empty($criteria) ? 20 : 10;
            $max_npp = sfConfig::get('app_list_full_max_npp');
            $this->setTemplate('../../documents/templates/listfull');
        }
        elseif (in_array('cond', $format))
        {
            $default_npp = empty($criteria) ? 20 : 10;
            $max_npp = sfConfig::get('app_list_conditions_max_npp');
        }
        elseif (in_array('json', $format))
        {
            $this->setJsonResponse();
            $default_npp = null;
            $max_npp = 100;
            $this->setTemplate('../../documents/templates/jsonlist');
        }
        else
        {
            $default_npp = null;
            if ($module == 'outings')
            {
                $perso = c2cPersonalization::getInstance();
                
                if (    empty($params_list)
                     && (   !$perso->areFiltersActive()
                         || !$perso->isMainFilterSwitchOn()
                         || $perso->areCacheableFilters($module)
                        )
                   )
                {
                    $default_npp = 60;
                }
            }
            
            if ($this->getUser()->hasCredential(sfConfig::get('app_credentials_moderator')))
            {
                $max_npp = 1000;
            }
            else
            {
                $max_npp = 100;
            }
            
            $this->setTemplate('../../documents/templates/list');
        }
        
        // DB request
        if ($criteria === 'no_result')
        {
            $nb_results = 0;
        }
        else
        {
            $sort = call_user_func(array('Document', 'getListSortCriteria'), $model, $default_npp, $max_npp);
            $page = $this->getRequestParameter('page', 1);
            $custom_fields = $this->getRequestParameter('data', '');
            $custom_fields = explode('-', $custom_fields);
            $infos = call_user_func(array('Document', 'browse'),
                                    $model,
                                    $sort,
                                    $criteria,
                                    $format,
                                    $page,
                                    0,
                                    $custom_fields);
            $nb_results = $infos['nb_results'];
            $this->pager = $infos['pager'];
            $this->query = $infos['query'];
            $this->activities = $infos['act'];
            $this->custom_fields = $custom_fields;
        }
        $this->nb_results = $nb_results;

        if (in_array('list', $format))
        {
            if ($nb_results == 1)
            {
                // if only one document matches, redirect automatically towards it
                c2cActions::statsdTiming('document.executeList.redirect', $timer->getElapsedTime('executeList'));
                $this->redirect('@document_by_id?module=' . $module . '&id=' . $infos['id']);
            }
            else
            {
                $params = c2cTools::getCriteriaRequestParameters(array(), true);
                if (isset($params['page']))
                {
                    unset($params['page']);
                }
                if (count($params))
                {
                    $this->getResponse()->addMeta('robots', 'noindex, nofollow');
                }
            }
        }

        // if there is no result + only criterias are on the name, redirect to a page wich loads google search
        if ($nb_results == 0 && !in_array('json', $format))
        {
            if (count($params_list) == 1)
            {
                $param = reset($params_list);
                if (strpos($param, 'nam') !== false)
                {
                    $this->query_string = $this->getRequestParameter($param);
                    $this->setTemplate('../../documents/templates/simplenoresult');
                }
            }
        }

        if ($nb_results > 0 && in_array($module, array('areas', 'articles', 'books', 'maps', 'portals', 'users')))
        {
            $items = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
            $this->items = Language::parseListItems($items, $model);
        }
        
        c2cActions::statsdTiming('document.executeList', $timer->getElapsedTime('executeList'));
    }

    /**
     * RSS version of list page
     */
    public function executeRss()
    {
        // TODO: factorize with list action?

        $timer = new sfTimer('executeRss');
        
        $module = $this->getModuleName();
        $model = $this->model_class;
        $criteria = $this->getListCriteria($model);
        
        if ($criteria !== 'no_result' && $module != 'documents')
        {
            $sort = call_user_func(array('Document', 'getListSortCriteria'), $model);
            $page = $this->getRequestParameter('page', 1);
            $infos = call_user_func(array('Document', 'browse'),
                                    $model,
                                    $sort,
                                    $criteria,
                                    array('rss'),
                                    $page);
            $nb_results = $infos['nb_results'];
            $this->pager = $infos['pager'];
            $this->query = $infos['query'];

            if ($nb_results > 0)
            {
                $items = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
                if (isset($items[0]['geoassociations']))
                {
                    $items = Language::getTheBestForAssociatedAreas($items);
                }
                // Retrieve creator and creation date.
                $items = Outing::getAssociatedCreatorData($items);
            }
            else
            {
                $items = array();
            }
        }
        else
        {
            $nb_results = 0;
            $items = array();
        }
        $this->nb_results = $nb_results;
        $this->items = $items;
        
        $this->setLayout(false);
        $this->setTemplate('../../documents/templates/rss');
        $this->setCacheControl();
        
        c2cActions::statsdTiming('document.executeRss', $timer->getElapsedTime('executeRss'));
    }

    public function executeWidget()
    {
        $this->div = $this->getRequestParameter('div', 'c2cwgt');
        $module = $this->getModuleName();
        $model = $this->model_class;
        $criteria = $this->getListCriteria($model);
        
        if ($criteria !== 'no_result' && $module != 'documents')
        {
            $sort = call_user_func(array('Document', 'getListSortCriteria'), $model);
            $page = $this->getRequestParameter('page', 1);
            $infos = call_user_func(array('Document', 'browse'),
                                    $model,
                                    $sort,
                                    $criteria,
                                    array('widget'),
                                    $page);
            $nb_results = $infos['nb_results'];
            $this->pager = $infos['pager'];
            $this->query = $infos['query'];

            if ($nb_results > 0)
            {
                $items = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
                $items = Language::parseListItems($items, $model);
            }
            else
            {
                $items = array();
            }
        }
        else
        {
            $nb_results = 0;
            $items = array();
        }
        $this->nb_results = $nb_results;
        $this->items = $items;

        $this->setLayout(false);
        $this->setTemplate('../../documents/templates/widget');
        $this->setCacheControl();
    }

    public function executeWidgetgenerator()
    {
      $this->setTemplate('../../documents/templates/widgetgenerator');
    }

    public function executeJson()
    {
        $format = $this->getRequestParameter('format', 'json');
        $format = explode('-', $format);
        if (!in_array('json', $format))
        {
            $format[] = 'json';
        }
        $this->format = $format;
        
        self::executeList();
    }

    public function executeGeojson()
    {
        $timer = new sfTimer('executeGeojson');
        $model = $this->model_class;
        $module = $this->getModuleName();
        $criteria = $this->getListCriteria($model);
        
        if ($criteria !== 'no_result' && $module != 'documents')
        {
            $sort = call_user_func(array('Document', 'getListSortCriteria'), $model);
            $page = $this->getRequestParameter('page', 1);
            $infos = call_user_func(array('Document', 'browse'),
                                    $model,
                                    $sort,
                                    $criteria,
                                    array('json'),
                                    $page);
            $nb_results = $infos['nb_results'];
            $this->pager = $infos['pager'];
            $this->query = $infos['query'];

            if ($nb_results > 0)
            {
                $items = $this->query->execute(array(), Doctrine::FETCH_ARRAY);
                $items = Language::parseListItems($items, $model);
            }
            else
            {
                $items = array();
            }
        }
        else
        {
            $nb_results = 0;
            $items = array();
        }
        $this->nb_results = $nb_results;
        $this->items = $items;
    
        $this->setTemplate('../../documents/templates/geojson');

        $this->setJsonResponse();
        c2cActions::statsdTiming('document.executeGeojson', $timer->getElapsedTime('executeGeojson'));
    }

    /**
     * RSS list of latest created documents.
     */
    public function executeLatest()
    {
        $timer = new sfTimer('executeLatest');
        $this->documents = Document::getLastDocs($this->__(' :').' ');
        $this->setLayout(false);
        $this->setCacheControl(3600);
        c2cActions::statsdTiming('document.executeLatest', $timer->getElapsedTime('executeLatest'));
    }

    /**
     * Get list of criteria used to filter items list.
     * Must be overridden in every module.
     * @return array
     */
    protected function getListCriteria($model)
    {
        $params_list = c2cTools::getCriteriaRequestParameters();
        
        Document::addParamForOrderby($params_list, $model);
        
        return call_user_func(array($model, 'buildListCriteria'), $params_list);
    }

    protected function JSONResponse($results)
    {
        $json_service = new Services_JSON();
        return $this->renderText($json_service->encode($results));
    }
    
    protected function getAreas($area_type, $separate_prefs = true)
    {
        $prefered_cultures = $this->getUser()->getCulturesForDocuments();
        $areas = Area::getRegions($area_type, $prefered_cultures);
        $area_names = $areas; // keep a copy with original names for translation

        // if user has some ranges as preferred areas, put them first, ordered alphabetically
        $prefered_ranges_assoc = array();
        if (is_array($separate_prefs))
        {
            $prefered_ranges = $separate_prefs;
            $separate_prefs = true;
            $separate_title = 'selected';
        }
        else
        {
            $prefered_ranges = c2cPersonalization::getInstance()->getPlacesFilter();
            $separate_title = 'prefered';
        }
        if ($separate_prefs && count($prefered_ranges))
        {
            // extract from $ranges the ranges whose key match the values of $prefered_ranges array:
            foreach ($prefered_ranges as $i => $id)
            {
                if (isset($areas[$id]))
                {
                    $prefered_ranges_assoc[$id] = $areas[$id];
                }
            }
            if (!empty($prefered_ranges_assoc))
            {
                // substract from this list those from personalization filter
                $areas = array_diff($areas, $prefered_ranges_assoc);

                // order alphabetically
                $prefered_temp = $prefered_ranges_assoc;
                array_walk($prefered_ranges_assoc, create_function('&$v, $k', '$v = remove_accents($v);'));
                asort($prefered_ranges_assoc, SORT_STRING);
                foreach($prefered_ranges_assoc as $key => &$value)
                {
                    $value = $area_names[$key];
                }
            }
        }

        // sort remaining areas alphabetically
        $area_type_list = sfConfig::get('app_areas_area_types');
        $area_type_name = $area_type_list[$area_type];

        // group areas, and sort them alphabetically inside each group (not for ranges)
        $order_alphabetically = ($area_type != 1);
        $unfiltered_areas_groups = sfConfig::get('app_areas_' . $area_type_name);
        $ordered_areas_groups = array();
        foreach ($unfiltered_areas_groups as $group_key => $unfiltered_areas)
        {
            $filtered_areas = array();
            foreach($unfiltered_areas as $area => $meta_id)
            {
                if (isset($areas[$area]))
                {
                    if (!$order_alphabetically)
                    {
                        $filtered_areas[$area] = $areas[$area];
                    }
                    else
                    {
                        $filtered_areas[$area] = remove_accents($areas[$area]);
                    }
                    unset($areas[$area]);
                }
            }

            if (count($filtered_areas))
            {
                if ($order_alphabetically)
                {
                    // now sort the areas inside
                    asort($filtered_areas, SORT_STRING);
                    foreach ($filtered_areas as $key => &$value)
                    {
                        $value = $area_names[$key];
                    }
                }
                $ordered_areas_groups[$this->__($group_key)] = $filtered_areas;
            }
        }
        // if there are areas that do not belong to a group, put them in an 'other regions' group
        if (count($areas))
        {
            array_walk($areas, create_function('&$v, $k', '$v = remove_accents($v);'));
            asort($areas, SORT_STRING);
            foreach ($areas as $key => &$value)
            {
                $value = $area_names[$key];
            }
            $ordered_areas_groups[$this->__('other '.$area_type_name)] = $areas;
        }

        if (count($prefered_ranges_assoc))
        {
            return array_merge(array($this->__($separate_title . ' ' . $area_type_name) => $prefered_ranges_assoc),
                               $ordered_areas_groups);
        }
        else
        {
            return $ordered_areas_groups;
        }

    }

    public function executeFilter()
    {
        $module = $this->getModuleName();
        
        $areas_type = $this->getRequestParameter('atyp', 1);
        
        $selected_areas = $this->getRequestParameter('areas', false);
        if ($selected_areas)
        {
            $selected_areas = explode('-', $selected_areas);
            $separate_prefs = $selected_areas;
        }
        else
        {
            if (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
            {
                $areas_type = intval($this->getRequest()->getCookie(sfConfig::get('app_personalization_cookie_places_type_name'), 1));
            }
            $selected_areas = array();
            $separate_prefs = true;
        }
        $ranges = $this->getAreas($areas_type, $separate_prefs);

        $this->ranges = $ranges;
        $this->areas_type = $areas_type;
        $this->selected_areas = $selected_areas;

        $activities = $this->getRequestParameter('act', null);
        if (preg_match('/^([0-9])(-[0-9])*$/', $activities, $regs))
        {
            $activities = explode('-', $activities);
        }
        else
        {
            $activities = array();
        }
        $this->activities = $activities;

        $this->setPageTitle($this->__('Search a ' . $module));
        $this->setTemplate('../../documents/templates/filter');
    }
    
    public function executeFilterredirect()
    {
        $route = '@default?action=list&module=' . $this->getModuleName(); 
        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $criteria = array_merge($this->filterSearchParameters(),
                                    $this->filterSortParameters());
            if ($criteria)
            {
                $route .= '&' . implode('&', $criteria);
            }
        }
        c2cTools::log("redirecting to $route");
        $this->redirect($route);
    }
    
    public function executeListredirect()
    {
        if ($this->getRequestParameter('commit'))
        {
            $result_type = $this->getRequestParameter('result_type');
            $linked_docs = $this->getRequestParameter('linked_docs');
        }
        else
        {
            $result_type = $this->getRequestParameter('result_type_2');
            $linked_docs = $this->getRequestParameter('linked_docs_2');
        }
        
        switch ($result_type)
        {
            case 0 : $module = $this->getModuleName(); break;
            case 1 : $module = 'routes'; break;
            case 2 : $module = 'outings'; break;
            case 3 : $module = 'outings'; break;
            default: $module = $this->getModuleName();
        }
        if ($result_type == 3)
        {
            $action = 'conditions';
        }
        else
        {
            $action = 'list';
        }
        $route = '/' . $module . '/' . $action; 
        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $criteria = array_merge($this->listSearchParameters($module, $linked_docs),
                                    $this->filterSortParameters($module));
            if ($criteria)
            {
                $route .= '?' . implode('&', $criteria);
            }
        }
        c2cTools::log("redirecting to $route");
        $this->redirect($route);
    }

    public function executePortalredirect()
    {
        $module = $this->getRequestParameter('wtype');
        $params = $this->getRequestParameter('params');
        $query_string = $this->getRequestParameter('q');
        if ($query_string)
        {
            list($module, $module_params) = explode('/',$module, 2);
            if (!empty($module_params))
            {
                $params = $module_params;
            }
            if ($module && in_array($module, sfConfig::get('app_modules_list')))
            {
                $model = c2cTools::module2model($module);
            }
            else
            {
                $model = 'Document';
                $module = 'documents';
            }
            
            $perso = c2cPersonalization::getInstance();
            if ($perso->isMainFilterSwitchOn())
            {
                $langs      = $perso->getLanguagesFilter();
                $ranges     = $perso->getPlacesFilter();
                $activities = $perso->getActivitiesFilter();
            }
            else
            {
                $langs = $ranges = $activities = array();
            }
            
            sfLoader::loadHelpers(array('Pagination'));
            $url_params = array();
            unpackUrlParameters($params, $url_params);
            
            $field = 'name';
            switch ($module)
            {
                case 'documents' :
                    $order = 'orderby=module&order=desc';
                    break;
                case 'portals' :
                    $field = 'wnam';
                    $order = 'orderby=wnam&order=asc';
                    break;
                case 'summits' :
                    $field = 'snam';
                    $order = 'orderby=snam&order=asc';
                    break;
                case 'sites' :
                    $field = 'tnam';
                    $order = 'orderby=snam&order=asc';
                    break;
                case 'routes' :
                    $field = 'srnam';
                    $order = 'orderby=rnam&order=asc';
                    break;
                case 'parkings' :
                    $field = 'pnam';
                    $order = 'orderby=pnam&order=asc';
                    break;
                case 'huts' :
                    $field = 'hnam';
                    $order = 'orderby=hnam&order=asc';
                    break;
                case 'products' :
                    $field = 'fnam';
                    $order = 'orderby=fnam&order=asc';
                    break;
                case 'outings' :
                    $field = 'onam';
                    $order = 'orderby=date&order=desc';
                    break;
                case 'areas' :
                    $field = 'anam';
                    $order = 'orderby=anam&order=asc';
                    break;
                case 'maps' :
                    $field = 'mnam';
                    $order = 'orderby=mnam&order=asc';
                    break;
                case 'books' :
                    $field = 'bnam';
                    $order = 'orderby=bnam&order=asc';
                    break;
                case 'articles' :
                    $field = 'cnam';
                    $order = 'orderby=cnam&order=asc';
                    break;
                case 'images' :
                    $field = 'inam';
                    $order = '';
                    break;
                case 'users' :
                    $field = 'utfnam'; // utfnam = unam + ufnam
                    $order = 'orderby=unam&order=asc';
                    break;
                default :
                    $order = '';
                    break;
            }
                
            $query_string = trim(str_replace(array('   ', '  ', '.'), array(' ', ' ', '%2E'), $query_string));
            $url_params[] = "$field=$query_string";
            $url_params[] = $order;
            
            $route = '/' . $module . '/list?' . implode('&', $url_params);
            c2cTools::log("redirecting to $route");
            $this->redirect($route);
        }
        else
        {
            $this->forward404('need a string');
        }
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

    protected function listSearchParameters($result_type = null, $linked_docs = 0)
    {
        $out = array();
        $module = $this->getModuleName();

        if ($linked_docs == 1)
        {
            sfLoader::loadHelpers(array('Pagination'));
            $rename = array();
            if ($module != $result_type)
            {
                $rename['id'] = $module;
            }
            $params = $this->getRequestParameter('params');
            unpackUrlParameters($params, $out, $rename);
        }
        elseif ($linked_docs == 2)
        {
            $rename = '';
            if ($module != $result_type)
            {
                $rename = $module;
            }
            $this->addListParam($out, 'id', $rename, '_');
        }
        
        return $out;
    }

    /**
     * Parses REQUEST sent by filter form and keeps only relevant sort parameters.
     * @return array
     */
    protected function filterSortParameters($result_type = null)
    {
        $sort = array();

        if (($npp = $this->getRequestParameter('npp')) && 
             $npp != (c2cTools::mobileVersion() ? sfConfig::get('app_list_mobile_maxline_number')
                                                : sfConfig::get('app_list_maxline_number')))
        {
            $sort[] = "npp=$npp";
        }

        $module = $this->getModuleName();
        if ($module != $result_type && $result_type == 'outings')
        {
            $sort[] = "orderby=date";
            $sort[] = "order=desc";
        }
        elseif (is_null($result_type) || $module == $result_type)
        {
            $default_order = sfConfig::get('app_list_default_order', 'asc');
            
            $has_orderby = $this->addParam($sort, 'orderby');
            if ($has_orderby)
            {
                $this->addParam($sort, 'order', $default_order);
            }
            $has_orderby = $this->addParam($sort, 'orderby2');
            if ($has_orderby)
            {
                $this->addParam($sort, 'order2', $default_order);
            }
            $has_orderby = $this->addParam($sort, 'orderby3');
            if ($has_orderby)
            {
                $this->addParam($sort, 'order3', $default_order);
            }
        }

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
        $id = $this->getRequestParameter('id');
        $this->pseudo_id = $this->getRequestParameter('pseudo_id');

        if (empty($id) && !empty($this->pseudo_id))
        {
            // Means that user resubmitted original form with id info missing.
            // We get it using a cookie in which the id was stored when the doc
            // was created.
            $id = $this->getRequest()->getCookie($this->pseudo_id);
        }
        
        if (!empty($id)) // update an existing document
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
            // create a new document
            $document = new $this->model_class;
            $this->document = $document;
            
            // we populate here some fields, for instance if we are creating a new outing, already associated with a route.
            $this->populateCustomFields();

            // here, filter edits which must require additional parameters (link for instance : outing with route)
            $this->filterAdditionalParameters();
            
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

        // All modules will use the same template
        $this->setTemplate('../../documents/templates/edit');

        $document = $this->document;
        $module_name = $this->getModuleName();
        $this->document_name = $document->get('name');

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
        
            // upload potential GPX file to server and set WKT field
            // or upload a new version of an image
            $request = $this->getRequest();
            
            if ($request->hasFiles())
            {
                c2cTools::log('request has files');

                if ($request->getFileName('image_new_version') && $module_name == 'images') // new image version
                {
                    c2cTools::log('new image uploaded');
                    $base_path = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR;
                    $temp_dir = $base_path . sfConfig::get('app_images_temp_directory_name');
                    $upload_dir = $base_path . sfConfig::get('app_images_directory_name');
                    $filename = $request->getFiles();
                    $unique_filename = c2cTools::generateUniqueName();
                    $file_ext = Images::detectExtension($filename['image_new_version']['tmp_name']);

                    // upload file in a temporary folder
                    $new_location = $temp_dir . DIRECTORY_SEPARATOR . $unique_filename . $file_ext;

                    sfLoader::loadHelpers(array('General'));
                    $redir_route = '@document_by_id_lang_slug?module=' . $module_name .
                        '&id=' . $this->document->get('id') .
                        '&lang=' . $this->document->getCulture() .
                        '&slug=' . get_slug($this->document);

                    if (!$request->moveFile('image_new_version', $new_location))
                    {
                        return $this->setErrorAndRedirect('Failed moving uploaded file', $redir_route);
                    }
                    if ($file_ext == '.svg')
                    {
                        if (!SVG::rasterize($temp_dir . DIRECTORY_SEPARATOR, $unique_filename, $file_ext))
                        {
                            return $this->setErrorAndRedirect('Failed rasterizing svg file', $redir_route);
                        }
                        $document->set('has_svg', true);
                    }
                    else
                    {
                        $document->set('has_svg', false);
                    }

                    // generate thumbnails (ie. resized images: "BI"/"SI")
                    Images::generateThumbnails($unique_filename, $file_ext, $temp_dir);
                    // move to uploaded images directory
                    if (!Images::moveAll($unique_filename . $file_ext, $temp_dir, $upload_dir))
                    {
                        return $this->setErrorAndRedirect('image dir unavailable', $redir_route);
                    }
                    // update filename and image properties
                    $document->set('filename', $unique_filename . $file_ext);
                    $size = getimagesize($upload_dir . DIRECTORY_SEPARATOR . $unique_filename . $file_ext);
                    if ($size)
                    {
                        $document->set('width', $size[0]);
                        $document->set('height', $size[1]);
                    }
                    $document->set('file_size', filesize($upload_dir . DIRECTORY_SEPARATOR . $unique_filename . $file_ext));
                    // populate with new exif data, if any...
                    $document->populateWithExifDataFrom($upload_dir . DIRECTORY_SEPARATOR . $unique_filename . $file_ext);
                }

                if ($request->getFileName('gps_data') && in_array($module_name, array('routes', 'outings'))) // gpx file
                {
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
                    else
                    {
                        $this->getRequest()->setError('gps_data', 'invalid gpx file');
                        return false;
                    }
                }
            }

            if (count($this->document->getModified()) == 0 &&
                count($this->document->getCurrentI18nObject()->getModified()) == 0)
            {
                // no change of the document was detected 
                // => redirects to the document without saving anything
                $this->redirectToView();
                return;
            }

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
                $message = (!$message) ? "Edit in $lang" : $message;
                            
                // if document has a geometry, compute and create associations with ranges, depts, countries.
                // nb: association is performed upon document creation with initial geometry
                // OR when the centroid (lon, lat) has moved during an update. 
                
                $needs_geom_association = isset($wkt) ||  // gpx or kml file has been uploaded 
                                          ($document->get('lon') != $old_lon && $document->get('lon') != null) || 
                                          ($document->get('lat') != $old_lat && $document->get('lat') != null); // geom centroid has moved
    
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
                    $nb_created = gisQuery::createGeoAssociations($id, true, ($module_name != 'outings')); 
                    c2cTools::log("created $nb_created geo associations");
                    
                    // if summit or site coordinates have moved (hence $needs_geom_association = true), 
                    // refresh geo-associations of associated routes (from summits) and outings (from routes or sites)
                    $this->refreshGeoAssociations($id);
                }
                
                // we clear views, histories, diffs of this doc + filter and list, in every language (content+interface):
                $this->clearCache($module_name, $id);
                
                // we clear views of the associated docs in every language (content+interface):
                // find all associated docs
                // 'users' module is excuded because a change of a user page have no visibility in associated docs, then it is not necessary to clear the cache of all associated outings
                if ($module_name != 'users')
                {
                    $associated_docs = Association::findAllAssociatedDocs($id, array('id', 'module'));
                    $ids = array();
                    foreach ($associated_docs as $doc)
                    {
                        $doc_id = $doc['id'];
                        $doc_module = $doc['module'];
                        
                        // clear their view cache
                        $this->clearCache($doc_module, $doc_id, false, 'view');
                        
                        if (        $module_name == 'outings'
                                 && in_array($doc_module, array('routes', 'sites'))
                             ||     $module_name == 'routes'
                                 && in_array($doc_module, array('summits', 'parkings'))
                           )
                        {
                            $ids[] = $doc_id;
                        }
                    }
                    
                    if ($module_name == 'outings')
                    {
                        $associated_docs = Association::findAllAssociatedDocs($ids, array('id', 'module'), array('sr', 'hr', 'pr', 'rr', 'pt', 'ht'));
                        $ids = array();
                        foreach ($associated_docs as $doc)
                        {
                            $doc_id = $doc['id'];
                            $doc_module = $doc['module'];
                            
                            // clear their view cache
                            $this->clearCache($doc_module, $doc_id, false, 'view');
                            
                            if (in_array($doc_module, array('summits', 'parkings', 'sites')))
                            {
                                $ids[] = $doc_id;
                            }
                        }
                        
                        if (count($ids))
                        {
                            $associated_docs = Association::findMainAssociatedDocs($ids, array('id', 'module'), array('ss', 'pp', 'tt'));
                            foreach ($associated_docs as $doc)
                            {
                                // clear their view cache
                                $this->clearCache($doc['module'], $doc['id'], false, 'view');
                            }
                        }
                    }
                    elseif ($module_name == 'routes')
                    {
                        $associated_docs = Association::findMainAssociatedDocs($ids, array('id', 'module'), array('ss', 'pp'));
                        foreach ($associated_docs as $doc)
                        {
                            // clear their view cache
                            $this->clearCache($doc['module'], $doc['id'], false, 'view');
                        }
                    }
                }

                // saves new document id in a "pseudo id" cookie to retrieve it if user resubmits original form
                if ($this->new_document && $this->pseudo_id)
                {
                    $this->getResponse()->setCookie($this->pseudo_id, $id);
                }
            }
        }

        // Go through simple heuristics to check for potential vandalism
        Vandalism::check($this->document);

        // module specific actions
        $this->endEdit();
    }

    /** nothing by default, overriden in child classes */
    public function executeRefreshgeoassociations()
    {
        /* nothing */
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
            $this->redirectToView();
        }
    }

    protected function redirectToView()
    {
        sfLoader::loadHelpers(array('General'));
        $this->redirect('@document_by_id_lang_slug?module=' . $this->getModuleName() .
                        '&id=' . $this->document->get('id') .
                        '&lang=' . $this->document->getCulture() .
                        '&slug=' . get_slug($this->document));
    }
    
    /**
     * Returns a 3D or 4D WKT 
     */
    protected function getWktFromFileUpload($request)
    {
        $path = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . c2cTools::generateUniqueName(); 
        $status = $request->moveFile('gps_data', $path);
        $type = c2cTools::getFileType($path); 
        c2cTools::log("File uploaded to: $path with status: $status and a file type of: $type");
        
        $finalPath = $path;
        $wkt = NULL;
        // we rename file according to true type
        // FIXME as for now we only accept gpx,
        // this is kinda redundant...
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
        // Get column information to provide with default values if needed
        $columns = Document::getColumnsInfo($this->model_class);
 
        foreach (Document::getVisibleFieldNamesByModel($this->model_class) as $field_name)
        {
            if ($this->hasRequestParameter($field_name))
            {
                $field_value = $this->getRequestParameter($field_name);
                $document->set($field_name, $field_value);
            }
            else
            {
                 // This paramater hasn't been POSTed, but we may want to give it a default value
                 $colInfo = $columns[$field_name];
                 switch( $colInfo['type'] )
                 {
                     case 'boolean':
                         // If no info is given on a boolean, we set it to false
                         $document->set($field_name, false);
                         break;
/*
                     // Added here in case there values need to be modified
                     // but it currently looks like leaving them untouched is fine
                     // these are the types in use on Feb19, 2010
                     case 'integer':
                     case 'smallint':
                     case 'double':
                     case 'character':
                     case 'text':
                     case 'string':
                     case 'timestamp':
                     case 'date':
                         $document->set($field_name, '');
                         break;
*/
                 }
            }
        }
    }

    public function executeWhatsnew()
    {
        $user_id = $this->getRequestParameter('users', null);
        $editedby_id = $this->getRequestParameter('editedby', null);
        $createdby_id = $this->getRequestParameter('createdby', null);
        $lang = $this->getRequestParameter('lang', null);
        $areas = $this->getRequestParameter('areas', null);
        $activities = $this->getRequestParameter('act', null);
        $mode = $this->getRequestParameter('mode', 'editions');
        if ($mode != 'creations')
        {
            $mode = 'editions';
        }
        $doc_id = $this->getRequestParameter('id', null);
        $params = array();
        $params['dtyp'] = $this->getRequestParameter('dtyp', null);
        $params['ctyp'] = $this->getRequestParameter('ctyp', null);
        $params['ityp'] = $this->getRequestParameter('ityp', null);
        
        $model = $this->model_class;
        $model_i18n = $model . 'I18n';
    
        $this->pager = Document::listRecentChangesPager($model, $lang, $areas, $activities, $doc_id, $user_id, $editedby_id, $createdby_id, $mode, $params);
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();

        $items = $this->pager->getResults('array');

        // prepend summit name to routes
        $module = $this->getModuleName();
        if ($module == 'routes' || $module == 'documents')
        {
            // adapt some keys in order to use Route::addBestSummitName
            foreach ($items as $key => $item)
            {
                $items[$key]['id'] = $item['document_id'];
                $items[$key]['name'] = $item[$model_i18n]['name'];
                $items[$key]['module'] = ($module == 'routes') ? 'routes' : $item[$model]['module'];
            }

            if ($module == 'routes')
            {
                $items = Route::addBestSummitName($items, $this->__(' :').' ');
            }
            else
            {
                $routes = Route::addBestSummitName(array_filter($items, array('c2cTools', 'is_route')), $this->__(' :').' ');
                foreach ($routes as $key => $route)
                {
                    $items[$key] = $route;
                }
            }

            foreach ($items as $key => $item)
            {
                $items[$key][$model_i18n]['name'] = $item['name'];
            }
        }
        
        $this->items = $items;
        $this->mode = $mode;


        $this->setTemplate('../../documents/templates/whatsnew');
        
        $page_title = ucfirst($this->__($module)) . ' :: ';
        if ($mode == 'creations')
        {
            $page_title .= $this->__('creations');
        }
        else
        {
            $page_title .= $this->__('changes');
        }
        $this->setPageTitle($page_title);
    }
 
    public function executeLatestassociations()
    {
        $doc_id = $this->getRequestParameter('id', null);
        $users = $this->getRequestParameter('users', null);
        $orderby = $this->getRequestParameter('orderby', null);
        $default_npp = sfConfig::get('app_list_maxline_number');
        $npp = $this->getRequestParameter('npp', $default_npp);
        if ($this->getUser()->hasCredential(sfConfig::get('app_credentials_moderator')))
        {
            $max_npp = 1000;
        }
        else
        {
            $max_npp = 100;
        }
        $npp = min($npp, $max_npp);
        $this->pager = AssociationLog::listRecentChangesPager($doc_id, $users, $orderby, $npp);
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();

        // prepend summit name for routes
        $items = $this->pager->getResults('array');
        $langs = $this->getUser()->getPreferedLanguageList();
        $items = Language::getTheBest($items, 'main', $langs, 'associations_log_id', true);
        $items = Language::getTheBest($items, 'linked', $langs, 'associations_log_id', true);
        $routes = array();
        foreach ($items as $key => $item)
        {
            $models = c2cTools::Type2Models($item['type']);
            $main_module = c2cTools::model2module($models['main']);
            $linked_module = c2cTools::model2module($models['linked']);
            $route_types = array();
            if ($main_module == 'routes' && !empty($item['mainI18n'])) $route_types[] = 'main';
            if ($linked_module == 'routes' && !empty($item['linkedI18n'])) $route_types[] = 'linked';
            foreach ($route_types as $type)
            {
                $routes[$type.'_'.$key]['id'] = $item[$type.'_id'];
                $routes[$type.'_'.$key]['name'] = $item[$type.'I18n'][0]['name'];
            }
        }
        $routes = Route::addBestSummitName($routes, $this->__(' :').' '); // TODO find a way to have the non breakable space despite the escaping
        foreach ($routes as $key => $route)
        {
            list($type, $k) = explode('_', $key);
            $items[$k][$type.'I18n'][0]['name'] = $route['name'];
        }
        $this->items = $items;

        $this->setTemplate('../../documents/templates/latestassociations');
        $this->setPageTitle($this->__('Recent associations'));
    }

    // quick search box in the header
    public function executeSearch()
    {
        if ($query_string = $this->getRequestParameter('q'))
        {
            if (($module = $this->getRequestParameter('type')) &&
                in_array($module, sfConfig::get('app_modules_list')))
            {
                $model = c2cTools::module2model($module);
            }
            else if ($module == 'forums')
            {
                $search_langs = implode(',', $langs);
                $search_location = "Location: /forums/search.php?action=search&keywords=$query_string&author=&forum[]=-1&lang=$search_langs&search_in=topic&sort_by=0&sort_dir=DESC&show_as=topics&search=Submit";
                header($search_location);
                exit;
            }
            else
            {
                $model = 'Document';
                $module = 'documents';
            }

            $field = 'name';
            switch ($module)
            {
                case 'documents' :
                    $order = '&orderby=module&order=desc';
                    break;
                case 'summits' :
                    $field = 'snam';
                    $order = '&orderby=snam&order=asc';
                    break;
                case 'sites' :
                    $field = 'tnam';
                    $order = '&orderby=snam&order=asc';
                    break;
                case 'routes' :
                    $field = 'srnam';
                    $order = '&orderby=rnam&order=asc';
                    break;
                case 'parkings' :
                    $field = 'pnam';
                    $order = '&orderby=pnam&order=asc';
                    break;
                case 'huts' :
                    $field = 'hnam';
                    $order = '&orderby=hnam&order=asc';
                    break;
                case 'outings' :
                    $field = 'onam';
                    $order = '&orderby=date&order=desc';
                    break;
                case 'areas' :
                    $field = 'anam';
                    $order = '&orderby=anam&order=asc';
                    break;
                case 'maps' :
                    $field = 'mnam';
                    $order = '&orderby=mnam&order=asc';
                    break;
                case 'books' :
                    $field = 'bnam';
                    $order = '&orderby=bnam&order=asc';
                    break;
                case 'articles' :
                    $field = 'cnam';
                    $order = '&orderby=cnam&order=asc';
                    break;
                case 'images' :
                    $field = 'inam';
                    $order = '';
                    break;
                case 'users' :
                    $field = 'utfnam'; // utfnam = unam + ufnam
                    $order = '&orderby=unam&order=asc';
                    break;
                default :
                    $order = '';
                    break;
            }
                
            $query_string = trim(str_replace(array('   ', '  ', '.'), array(' ', ' ', '%2E'), $query_string));
            $this->redirect(sprintf("%s/list?$field=%s$order", $module, $query_string));
        }
        else
        {
            $this->forward404('need a string');
        }
    }

    // ajax autocomplete version of the above function
    // this is only used in the header
    public function executeQuicksearch()
    {
        $query_string = $this->getRequestParameter('q');
        if (($module = $this->getRequestParameter('type')) &&
            in_array($module, sfConfig::get('app_modules_list')))
        {
            $model = c2cTools::module2model($module);
        }
        else if ($module == 'forums')
        {
            return $this->renderText('<ul></ul>');
        }
        else
        {
            $model = 'Document';
            $module = 'documents';
        }

        // search
        $items = Document::quickSearchByName($query_string, $model);
        $nb_results = count($items);
        
        if ($nb_results == 0 || $nb_results > sfConfig::get('app_autocomplete_max_results'))
        {
            return $this->renderText('<ul></ul>');
        }

        $items = Language::getTheBest($items, $model);

        // FIXME this part is a bit dirty..
        // + can't we do it in only one request?
        if ($model == 'Route')
        {
            $routes = array();
            foreach ($items as $item)
            {
                $routes[] = array('id' => $item['id'],
                                  'name' => $item[$model . 'I18n'][0]['name'],
                                  'activities' => $item['activities']);
            }
            $items = Route::addBestSummitName($routes, $this->__(' :').' ');
        }

        // if module = summit, site, parking or hut, check for similarities and if any, append regions for disambiguation
        if ($model == 'Summit' || $model == 'Site' || $model == 'Parking' || $model == 'Hut')
        {
            sfLoader::loadHelpers(array('General'));
            $items_copy = $items;
            for ($i=1; $i<count($items); $i++)
            {       
                $item_cmp = array_shift($items_copy);
                foreach ($items_copy as $item)
                {   
                   if (levenshtein(remove_accents($item_cmp[$model . 'I18n'][0]['name']),
                                   remove_accents($item[$model . 'I18n'][0]['name'])) <= 2)
                   {
                       $add_region = true;
                       break 2;
                   }
                }
            }
        }
        if (isset($add_region)) // We append best region name
        {
            $tmp = array();
            foreach ($items as $item)
                $tmp[$item['id']] = $item;
            $items = $tmp;

            // retrieve attached regions best names
            $q = Doctrine_Query::create()
                ->select('m.id, g0.main_id, a.area_type, ai.name, ai.culture')
                ->from("$model m")
                ->leftJoin("m.geoassociations g0")
                ->leftJoin('g0.AreaI18n ai')
                ->leftJoin('ai.Area a')
                ->addWhere('g0.main_id IN (' . implode(',', array_keys($items)) . ')')
                ->addWhere("g0.type != 'dm'")
                ->execute(array(), Doctrine::FETCH_ARRAY);
            $areas_array = Language::getTheBestForAssociatedAreas($q);

            // choose the best area description (like in homepage)
            foreach ($areas_array as $item)
            {
                $area_name = Area::getBestRegionDescription($item['geoassociations']);
                if (!empty($area_name))
                {
                    $items[$item['id']]['area_name'] = $area_name;
                }
            }
        }

        $html = '<ul>';
        foreach ($items as $item)
        {
            $name = isset($item[$model . 'I18n']) ? $item[$model . 'I18n'][0]['name'] : '';
            $suffix = '';

            switch($model)
            {
                case 'Route':
                    sfLoader::loadHelpers(array('I18N', 'Pagination'));
                    $name = $item['name'];
                    $suffix = get_paginated_activities($item['activities']);
                    break;
                case 'User':
                    $suffix = '<em>('.$item['private_data']['username'].')</em>';
                    break;
                case 'Summit':
                case 'Site':
                case 'Parking':
                case 'Hut':
                    if (isset($item['area_name']))
                        $suffix = '<em>('.$item['area_name'].')</em>';
                    break;
                case 'Document': 
                    $suffix = '<em>('.$this->__(substr($item['module'], 0, -1)).')</em>';
                    break;
            }

            $html .= '<li id="'.$item['id'].'">'.$name.
                     (empty($suffix) ? '' : ' '.$suffix).'</li>';
        }
        $html .= '</ul>';

        return $this->renderText($html);
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

    public function executeDeleteculture()
    {
        $referer = $this->getRequest()->getReferer();

        if ($this->hasRequestParameter('id') && $this->hasRequestParameter('lang'))
        {
            $id = $this->getRequestParameter('id');
            $lang = $this->getRequestParameter('lang');

            $document = Document::find($this->model_class, $id, array('module'));

            if (!$document)
            {
                $this->setErrorAndRedirect('Document does not exist', $referer);
            }

            $module = $document->get('module');

            // check that the document exists in the requested culture, and at least one more
            $available_cultures = $document->getLanguages();
            if (count($available_cultures) < 2 || !isset($available_cultures[$lang]))
            {
                $this->setErrorAndRedirect('You cannot delete this document culture', $referer);
            }

            $nb_dv_deleted = Document::doDelete($id, $lang);
            c2cTools::log("executeDeleteCulture: deleted document $id in $lang and its $nb_dv_deleted versions");

            if ($nb_dv_deleted)
            {
                // cache clearing
                $this->clearCache($module, $id, false);
                $this->setNoticeAndRedirect('Document culture deleted', "@document_by_id?module=$module&id=$id");
            }
            else
            {
                $this->setErrorAndRedirect('Could not understand your request', $referer);
            }
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
            $this->clearCache($this->getModuleName(), $id, false);
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
        
        if (!($from_id))
        {
            $this->setErrorAndRedirect('Could not understand your request', $referer);
        }
        
        if ($from_id == $to_id)
        {
            $this->setErrorAndRedirect('Could not perform merge with itself', $referer);
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

            $this->doMerge($from_id, $document_from, $to_id, $document_to);

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

    protected function doMerge($from_id, $document_from, $to_id, $document_to)
    {
        $module = $this->getModuleName();
        $user_id = $this->getUser()->getId();
        $conn = sfDoctrine::Connection();
        try
        {
            $conn->beginTransaction();

            // merging consists of a redirection and an edition blocking :
            $document_from->set('redirects_to', $to_id);
            $document_from->set('is_protected', true);

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
        sfLoader::loadHelpers(array('General'));

        $model = $this->model_class;
        $module = c2cTools::model2module($model);
        $string = $this->getRequestParameter($module.'_name');

        // useful protection:
        if (strlen($string) < sfConfig::get('app_autocomplete_min_chars')) // typically 3 or 4
        {
            return $this->renderText('<ul></ul>');
        }

        // return all documents matching $string in given module/model
        // NB: autocomplete on outings only returns those for which the current user is linked to
        // autocomplete on articles and images only returns collaborative and proper personal ones (all for moderators)
        $user = $this->getUser();
        $filter_personal_content = !$user->hasCredential('moderator') && ($module == 'articles' || $module == 'images');
        
        $results = Document::searchByName($string, $model, $user->getId(), $filter_personal_content);
        $nb_results = count($results);

        if ($nb_results == 0)
        {
            return $this->ajax_feedback_autocomplete('no results');
        }

        if ($nb_results > sfConfig::get('app_autocomplete_max_results') && $nb_results < sfConfig::get('app_list_maxline_number'))
        {
            // if there are too many results to display, but if there is at least one exact match, it is in the results return by the db query
            // we display the exact matches, if any. We translate some special chars and capital letters
            sfLoader::loadHelpers(array('General'));
            $simple_string = remove_accents($string);
            $exact_matches = array();
            foreach ($results as $result)
            {
                if ((remove_accents($result[$this->model_class . 'I18n'][0]['name']) == $simple_string) ||
                    ($module == 'users' && remove_accents($result['private_data']['username']) == $simple_string))
                {
                    $exact_matches[] = $result;
                }
            }

            if (count($exact_matches))
            {
                $results = $exact_matches;
            }
            else
            {
                return $this->ajax_feedback_autocomplete('Too many results. Please go on typing...');
            }
        }
        elseif ($nb_results == sfConfig::get('app_list_maxline_number'))
        {
            // we have the maximum number of results returned by the db query, so we assume there are more
            // we try to make an exact search directly to the db (they might not be in the few returned by the previous query)
            // Note that in this case, we don't try to simplify accents or capital letters
            $exact_results = Document::searchByName($string, $model, $user->getId(), $filter_personal_content, true);
            $nb_exact_results = count($exact_results);

            if ($nb_exact_results)
            {
                $results = $exact_results;
                $exact_matches = true;
            }
            else
            {
                return $this->ajax_feedback_autocomplete('Too many results. Please go on typing...');
            }
        }
        

        // build the actual results based on the user's prefered language
        $items = Language::getTheBest($results, $model);

        // if module = summit, site, parking or hut, check for similarities and if any, append regions for disambiguation
        if ($model == 'Summit' || $model == 'Site' || $model == 'Parking' || $model == 'Hut')
        {
            $items_copy = $items;
            for ($i=1; $i<count($items); $i++)
            {
                $item_cmp = array_shift($items_copy);
                foreach ($items_copy as $item)
                {
                   if (levenshtein(remove_accents($item_cmp[$this->model_class . 'I18n'][0]['name']),
                                   remove_accents($item[$this->model_class . 'I18n'][0]['name'])) <= 2)
                   {
                       $add_region = true;
                       break 2;
                   }
                }
            }
        }

        if (isset($add_region)) // We append best region name
        {
            // retrieve attached regions best names
            $q = Doctrine_Query::create()
                ->select('m.id, g0.main_id, a.area_type, ai.name, ai.culture')
                ->from("$model m")
                ->leftJoin("m.geoassociations g0")
                ->leftJoin('g0.AreaI18n ai')
                ->leftJoin('ai.Area a')
                ->addWhere('g0.main_id IN (' . implode(',', array_keys($items)) . ')')
                ->addWhere("g0.type != 'dm'")
                ->execute(array(), Doctrine::FETCH_ARRAY);
            $areas_array = Language::getTheBestForAssociatedAreas($q);

            // choose the best area description (like in homepage)
            foreach ($areas_array as $item)
            {
                $area_name = Area::getBestRegionDescription($item['geoassociations']);
                if (!empty($area_name))
                {
                    $items[$item['id']]['area_name'] = $area_name;
                }
            }    
        }

        // create list of items
        $html = '<ul>';
        foreach ($items as $item)
        {
            $identifier = ($model == 'Document') ? $this->__(substr($item['module'], 0, -1)) . ' ' : '' ; // if module = documents, add the object type inside brackets
            $postidentifier = ($model == 'Outing') ? ' (' . $item['date'] . ')' : ''; // if outings, we append the date
            $postidentifier = ($model == 'Summit') ? ' - ' . $item['elevation'] . $this->__('meters') : ''; // if summit, append elevation
            $postidentifier .= ($model == 'Book') ? ((check_not_empty($item['author']) ? ' - ' . $item['author'] : '') .
                                                     (check_not_empty($item['publication_date']) ? ' - ' . $item['publication_date'] : ''))
                                                  : ''; // if book, append author and publication date
            $postidentifier .= (isset($item['area_name'])) ? ' ('.$item['area_name'].')' : ''; // if region attached, we append it
            $postidentifier .= ($model == 'User') ? ' (' . $item['private_data']['username']. ')' : ''; // if user, append forum nickname
            $html .= '<li id="'.$item['id'].'">'.$item[$this->model_class . 'I18n'][0]['name'].'<span class="informal">'.
                     "<em>$postidentifier</em> <small>[$identifier" . $item['id'] . ']</small></span></li>';
        }
        if (isset($exact_matches))
        {
            $html .= '<div class="feedback">'.$this->__('only exact matches. Go on typing').'</div>';
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
        $feed = new sfGeoRssFeed();
        $lang = $this->getRequestParameter('lang');
        $model = $this->model_class;
        $module = $this->getModuleName();
        $id = $this->getRequestParameter('id');
        $mode = $this->getRequestParameter('mode');
        if ($lang)
        {
            $languages = sfConfig::get('app_languages_c2c');
        }

        switch ($mode)
        {
            case 'editions':
                $description = $this->__($lang ? "Latest %1% editions in %2%" : "Latest %1% editions",
                                         array('%1%' => $this->__($module), '%2%' => $lang ? $this->__($languages[$lang]) : null));
                $title = $description; // TODO Provide better description, different from title
                $link = "@module_whatsnew?module=$module"; // TODO maybe we should propose lang filters for whatsnew url?
                $self_link = $lang ? "@feed_lang?module=$module&lang=$lang" : "@feed?module=$module";
                break;
            case 'creations':
                $description = $this->__($lang ? "Latest %1% creations in %2%" : "Latest %1% creations",
                                        array('%1%' => $this->__($module), '%2%' => $lang ? $this->__($languages[$lang]) : null));
                $title = $description; // TODO Provide better description, different from title
                $link = "@default_index?module=$module"; // TODO can we redirect to list with lang filter once it is done?
                $self_link = $lang ? "@creations_feed_lang?module=$module&lang=$lang" : "@creations_feed?module=$module";
                break;
            default : // editions of a specific document
                // check that document $id exists in lang $lang, and retrieve its name.
                if (!$document = DocumentI18n::findName($id, $lang, $this->model_class))
                {
                    $this->setNotFoundAndRedirect();
                }
                $name = $document->get('name');
                $description = $this->__("Latest editions for \"%1%\" - %2%", array('%1%' => $name, '%2%' => $this->__($languages[$lang])));
                $title = $description; // TODO Provide better description, different from title
                $link = "@document_history?module=$module&id=$id&lang=$lang";
                $self_link = "@document_feed?module=$module&id=$id&lang=$lang";
                break;
        }

        $feed->setTitle($title);
        $feed->setLink($link);
        $feed->setFeedUrl($self_link);
        $feed->setDescription($description);
        $feed->setLanguage($lang);
        $feed->setAuthorName('Camptocamp.org');

        $max_number = sfConfig::get('app_feeds_items_limit');

        if ($module != 'documents' || !empty($id))
        {
            //usage: listRecent($model, $limit, $user_id = null, $lang = null, $doc_id = null, $mode = 'editions')
            $items = Document::listRecent($this->model_class, $max_number, null, $lang, $id, $mode);

            sfLoader::loadHelpers(array('General', 'SmartFormat'));
            
            $model_i18n = $model . 'I18n';

            // Add best summit name for routes
            foreach ($items as $key => $item)
            {
                $items[$key]['module'] = $item[$model]['module'];
                $items[$key]['id'] = $item['document_id'];
                $items[$key]['name'] = $item[$model_i18n]['name'];
                $items[$key]['search_name'] = $item[$model_i18n]['search_name'];
            }
            $routes = Route::addBestSummitName(array_filter($items, array('c2cTools', 'is_route')), $this->__(' :') . ' ');
            foreach ($routes as $key => $route)
            {
                $items[$key] = $route;
            }
            

            foreach ($items as $item)
            {
                $item_id = $item['document_id'];
                $new = $item['version'];
                $module_name = $item[$model]['module'];
                $name = $item['name'];
                $doc_lang = $item['culture'];
                $feedItemTitle = $name . (($mode != 'creations') ? (" - r$new" . ($lang ? '' : "/$doc_lang")) : '');

                $feedItem = new sfGeoFeedItem();
                $feedItem->setTitle($feedItemTitle);

                if ($mode == 'creations')
                {
                    if ($module_name == 'users')
                    {
                        $feedItem->setLink("@document_by_id_lang?module=$module_name&id=$item_id&lang=$doc_lang");
                    }
                    else
                    {
                        $feedItem->setLink("@document_by_id_lang_slug?module=$module_name&id=$item_id&lang=$doc_lang&slug=" .
                                           make_slug($item['name']));
                    }
                }
                else
                {
                    $feedItem->setLink("@document_by_id_lang_version?module=$module_name&id=$item_id&lang=$doc_lang&version=$new");
                }
                $feedItem->setAuthorName($item['history_metadata']['user_private_data']['topo_name']);
                //$feedItem->setAuthorEmail($item['history_metadata']['user_private_data']['email']);
                $feedItem->setPubdate(strtotime($item['created_at']));
                $feedItem->setUniqueId("$item_id-$doc_lang-$new");
                $feedItem->setLongitude($item[$model]['lon']);
                $feedItem->setLatitude($item[$model]['lat']);
                $comment = smart_format($item['history_metadata']['comment']);
                $feedItem->setDescription($comment);
                if ($mode != 'creations')
                {
                    $link = ($new > 1) ? ' - ' . link_to(__('View difference between version %1% and %2%', array('%1%' => ($new-1), '%2%' => $new)),
                                                         "@document_diff?module=$module_name&id=$item_id&lang=$doc_lang&new=$new&old=".($new-1),
                                                         array('absolute' => true)) : '';
                    $feedItem->setContent($comment . $link);
                }

                $feed->addItem($feedItem);
            }
        }

        $this->feed = $feed;
        $this->setTemplate('../../documents/templates/feed');
    }

    protected function setNotFoundAndRedirect()
    {
        $this->forward404();
        //$this->setErrorAndRedirect($this->model_class . ' not found',
        //                           '@default_index?module=' . $this->getModuleName(),
        //                           301);
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
    
    public function executeExportgpxversion()
    {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        $version = $this->getRequestParameter('version');
        
        $this->Export($module, $id, $lang, 'gpx', $version);
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

    public function executeExportkmlversion()
    {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        $version = $this->getRequestParameter('version');
        
        $this->Export($module, $id, $lang, 'kml', $version);
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
    
    public function executeExportjsonversion()
    {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        $lang = $this->getRequestParameter('lang');
        $version = $this->getRequestParameter('version');
        
        $this->Export($module, $id, $lang, 'json', $version);
    }

    /**
     * returns a GPX, KML or GeoJSON version of the document geometry with some useful additional informations in best possible lang
     */
    protected function Export($module, $id, $lang, $format, $version = null)
    {
        if (!$id) 
        {
            $this->setErrorAndRedirect('Could not understand your request',
                                        "@default_index?module=$module");
        }
        
        if (!empty($version))
        {
            $document = $this->getDocument($id, $lang, $version);
        }
        
        if (empty($document))
        {
            $document = Document::find('Document', $id, array('module', 'geom_wkt'));
        }
        
        if (!$document || $document->get('module') != $module)
        {
            $this->setErrorAndRedirect('Document does not exist',
                                        "@default_index?module=$module");
        }
        
        if ($document->get('geom_wkt'))
        {
            sfLoader::loadHelpers(array('General'));

            $doc = DocumentI18n::findNameDescription($id, $lang);
            // document can exist (id) but not in required lang (id, lang)
            if ($doc) 
            {
                $this->name = $doc->get('name');
                $this->description = $doc->get('description');
                $this->slug = get_slug($doc);
            }
            else
            {
                $this->name = 'C2C::' . ucfirst(substr($module, 0, strlen($module) - 1)) . " $id";
                $this->description = "";
                $this->slug = "";
            }
            
            $response = $this->getResponse();

            switch ($format)
            {
                case 'gpx':
                    $this->points = gisQuery::getEWKT($id, true, $module, $version);
                    $this->setTemplate('../../documents/templates/exportgpx'); 
                    $response->setContentType('application/gpx+xml'); 
                    break;
                case 'kml':
                    $this->points = gisQuery::getEWKT($id, true, $module, $version);
                    $this->setTemplate('../../documents/templates/exportkml'); 
                    $response->setContentType('application/vnd.google-earth.kml+xml');
                    break;
                case 'json':
                    $this->geojson = gisQuery::getGeoJSON($id, $module, $version, 6);
                    $this->setTemplate('../../documents/templates/exportjson');
                    $response->setContentType('application/json');
                    break;
            }
            $this->setLayout(false);
        }
        else
        {
            sfLoader::loadHelpers('General');
            $this->setErrorAndRedirect('This document has no geometry',
                                        "@document_by_id_lang_slug?module=$module&id=$id&lang=$lang&slug=" . get_slug($document));
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
            $nb_deleted = GeoAssociation::deleteAllFor($id, array('dm', 'dr', 'dd', 'dc', 'dv'));
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

    public function executeClearcache()
    {
        // check user is moderator: done in apps/frontend/config/security.yml
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');
        $referer = $this->getRequest()->getReferer();

        $this->clearCache($module, $id, false);

        return $this->setNoticeAndRedirect('Document cache has been cleared', $referer);
    }

    /**
     * Executes "associate current document with document" action
     * associated document can only be : articles, summits, books, huts, outings, routes, sites, users
     * ... restricted in security.yml to logged people
     */
    public function executeAddAssociation()
    {
        $user = $this->getUser();
        $user_id = $user->getId();
        $is_moderator = $user->hasCredential(sfConfig::get('app_credentials_moderator'));

        //
        // Get parameters and check that association is allowed
        //
        
        // if session is time-over
        if (!$user_id)
        {
            return $this->ajax_feedback('Session is over. Please login again.');
        }

        if (!$this->hasRequestParameter('document_id') || !$this->hasRequestParameter('main_id') ||
            !$this->hasRequestParameter('document_module'))
        {
            return $this->ajax_feedback('Operation not allowed');
        }

        $main_module = $this->getRequestParameter('module');
        $main_id = $this->getRequestParameter('main_id');
        $linked_module = $this->getRequestParameter('document_module');
        $linked_id = $this->getRequestParameter('document_id');
        $icon = $this->getRequestParameter('icon', '');
        $div = $this->getRequestParameter('div', false);
        
        if ($linked_id == $main_id )
        {
            return $this->ajax_feedback('A document can not be linked to itself');
        }

        switch ($linked_module)
        {
            case 'huts': $fields = array('id', 'is_protected', 'shelter_type'); break;
            case 'articles': $fields = array('id', 'is_protected', 'article_type'); break;
            case 'images': $fields = array('id', 'is_protected', 'image_type'); break;
            case 'documents': $fields = array('id', 'is_protected', 'module'); break; // FIXME prevent such case?
            default: $fields = array('id', 'is_protected'); break;
        }

        $linked_document = Document::find(c2cTools::module2model($linked_module), $linked_id, $fields);
        $linked_module = ($linked_module != 'documents') ? $linked_module : $linked_document->get('module');

        if (!$linked_document)
        {
            return $this->ajax_feedback('Linked document does not exist');
        }
        
        $type_modules = c2cTools::Modules2Type($main_module, $linked_module);
        
        if (empty($type_modules))
        {
            return $this->ajax_feedback('Wrong association type');
        }
        
        list($type, $swap, $main_module_new, $linked_module_new, $strict) = $type_modules;

        switch ($main_module)
        {
            case 'huts': $fields = array('id', 'is_protected', 'shelter_type'); break;
            case 'articles': $fields = array('id', 'is_protected', 'article_type'); break;
            case 'images': $fields = array('id', 'is_protected', 'image_type'); break;
            case 'documents': $fields = array('id', 'is_protected', 'module'); break; // FIXME prevent such case?
            default: $fields = array('id', 'is_protected'); break;
        }
        
        $main_document = Document::find(c2cTools::module2model($main_module), $main_id, $fields);
        
        if (!$main_document)
        {
            return $this->ajax_feedback('Main document does not exist');
        }
        
        if($swap)
        {
            $main_document_new = $linked_document;
            $main_id_new = $linked_id;
            $linked_document_new = $main_document;
            $linked_id_new = $main_id;
        }
        else
        {
            $main_document_new = $main_document;
            $main_id_new = $main_id;
            $linked_document_new = $linked_document;
            $linked_id_new = $linked_id;
        }

        if ($linked_module_new == 'articles')
        {
            if (!$is_moderator)
            {
                if (($linked_document_new->get('article_type') == 2) // only user linked to the personal article and moderators can associate docs
                    && !Association::find($user_id, $linked_id_new, 'uc'))
                {
                    return $this->ajax_feedback('You do not have the right to link a document to a personal article');
                }
                if ($main_module_new == 'articles')
                {
                    if (($main_document_new->get('article_type') == 2) // only user linked to the personal article and moderators can associate docs
                        && !Association::find($user_id, $main_id_new, 'uc'))
                    {
                        return $this->ajax_feedback('You do not have the right to link a document to a personal article');
                    }
                }
                if (($main_module_new == 'outings') && (!Association::find($user_id, $main_id_new, 'uo')))
                {
                    return $this->ajax_feedback('You do not have the right to link an article to another user outing');
                }
            }
            
            if (($linked_document_new->get('article_type') != 2) && ($type == 'uc')) // only personal articles (type 2) need user association
            {
                return $this->ajax_feedback('An user can not be linked to a collaborative article');
            }
        }

        if ($linked_module_new == 'images')
        {
            if ($main_document_new->get('is_protected') && !$is_moderator)
            {
                return $this->ajax_feedback('Document is
                protected');
            }
            if (!$is_moderator)
            {
                if ($main_module_new == 'users' && $main_id_new != $user_id)
                {
                    return $this->ajax_feedback('You do not have the right to link an image to another user profile');
                }
                if (($main_module_new == 'outings') && (!Association::find($user_id, $main_id_new, 'uo')))
                {
                    return $this->ajax_feedback('You do not have the right to link an image to another user outing');
                }
                if (($main_module_new == 'articles') && ($main_document_new->get('article_type') == 2) && (!Association::find($user_id, $main_id_new, 'uc')))
                {
                    return $this->ajax_feedback('You do not have the right to link an image to a personal article');
                }
                if (($main_module_new == 'images') && ($main_document_new->get('image_type') == 2) && ($document->getCreatorId() != $user_id))
                {
                    return $this->ajax_feedback('You do not have the right to link an image to a personal image');
                }
            }
        }
        
        if ($linked_module_new == 'outings')
        {
            if (!$is_moderator)
            {
                if (($main_module_new == 'users') && (!Association::find($user_id, $linked_id_new, 'uo')))
                {
                    return $this->ajax_feedback('You do not have the right to link an user to another user outing');
                }
                if (($main_module_new == 'routes') && (!Association::find($user_id, $linked_id_new, 'uo')))
                {
                    return $this->ajax_feedback('You do not have the right to link a route to another user outing');
                }
                if (($main_module_new == 'sites') && (!Association::find($user_id, $linked_id_new, 'uo')))
                {
                    return $this->ajax_feedback('You do not have the right to link a site to another user outing');
                }
            }
        }
        
        if ($linked_module_new == 'huts')
        {
            if ($main_module_new == 'summits')
            {
                $associations = Association::findAllAssociations($linked_id_new, 'sh');
                if (count($associations))
                {
                    return $this->ajax_feedback('This hut is already linked to a summit');
                }
            }
            if ($main_module_new == 'parkings')
            {
                $associations_pp = Association::findAllAssociations($main_id_new, 'pp');
                $associations_ph = Association::findAllAssociations($linked_id_new, 'ph');
                foreach ($associations_pp as $a_pp)
                {
                    foreach ($associations_ph as $a_ph)
                    {
                        if ($a_pp['main_id'] == $a_ph['main_id'] || $a_pp['linked_id'] == $a_ph['main_id'])
                        {
                            return $this->ajax_feedback('A parking can not be linked to a hut if a main/sub parking is already linked to it');
                        }
                    }
                }
            }
        }
        
        if ($linked_module_new == 'routes')
        {
            if ($main_module_new == 'huts' && $main_document_new->get('shelter_type') == 5)
            {
                return $this->ajax_feedback('A gite can not be linked to a route');
            }
            if ($main_module_new == 'parkings')
            {
                $associations_pp = Association::findAllAssociations($main_id_new, 'pp');
                $associations_pr = Association::findAllAssociations($linked_id_new, 'pr');
                foreach ($associations_pp as $a_pp)
                {
                    foreach ($associations_pr as $a_pr)
                    {
                        if ($a_pp['main_id'] == $a_pr['main_id'] || $a_pp['linked_id'] == $a_pr['main_id'])
                        {
                            return $this->ajax_feedback('A parking can not be linked to a route if a main/sub parking is already linked to it');
                        }
                    }
                }
            }
        }
        
        if ($linked_module_new == 'sites')
        {
            if ($main_module_new == 'sites')
            {
                if (Association::countAllMain(array($linked_id_new), 'tt'))
                {
                    return $this->ajax_feedback('A sub site can not be linked to more than one main site');
                }
            }
            if ($main_module_new == 'summits')
            {
                if (Association::countAllMain(array($linked_id_new), 'st'))
                {
                    return $this->ajax_feedback('A site can not be linked to more than one summit');
                }
                if (Association::countAllMain(array($linked_id_new), 'tt'))
                {
                    return $this->ajax_feedback('A summit can not be linked to a sub site');
                }
            }
            if ($main_module_new == 'parkings')
            {
                $associations_pp = Association::findAllAssociations($main_id_new, 'pp');
                $associations_pt = Association::findAllAssociations($linked_id_new, 'pt');
                foreach ($associations_pp as $a_pp)
                {
                    foreach ($associations_pt as $a_pt)
                    {
                        if ($a_pp['main_id'] == $a_pt['main_id'] || $a_pp['linked_id'] == $a_pt['main_id'])
                        {
                            return $this->ajax_feedback('A parking can not be linked to a site if a main/sub parking is already linked to it');
                        }
                    }
                }
            }
        }
        
        if ($linked_module_new == 'summits')
        {
            if ($main_module_new == 'summits')
            {
                if (Association::countAllMain(array($linked_id_new), 'ss'))
                {
                    return $this->ajax_feedback('A sub summit can not be linked to more than one main summit');
                }
            }
        }
        
        if ($linked_module_new == 'parkings')
        {
            if ($main_module_new == 'parkings')
            {
                if (Association::countAllMain(array($linked_id_new), 'pp'))
                {
                    return $this->ajax_feedback('A sub parking can not be linked to more than one main parking');
                }
            }
        }
        
        if (Association::find($main_id_new, $linked_id_new, $type, false))
        {
            return $this->ajax_feedback('The document is already linked to the current document');
        }

        if ($linked_module_new == 'outings' && $main_module_new == 'users' && $linked_id != $user_id)
        {
            // send an email to warn the new user associated
            $email_recipient = UserPrivateData::find($linked_id)->getEmail();
            $email_subject = $this->__('You have been associated to an outing');
            $server = $_SERVER['SERVER_NAME'];
            $outing_link = 'http'.(empty($_SERVER['HTTPS']) ? '' : 's')."://$server/outings/$main_id";
            $htmlBody = $this->__('You have been associated to outing %1% details', array('%1%' => '<a href="' . $outing_link . '">' . $outing_link . '</a>'));

            $mail = new sfMail();
            $mail->setCharset('utf-8');

            // definition of the required parameters
            $mail->setSender(sfConfig::get('app_outgoing_emails_sender'));
            $mail->setFrom(sfConfig::get('app_outgoing_emails_from'));
            $mail->addReplyTo(sfConfig::get('app_outgoing_emails_reply_to'));
            $mail->addAddress($email_recipient);
            $mail->setSubject($email_subject);
            $mail->setContentType('text/html');
            $mail->setBody($htmlBody);
            $mail->setAltBody(strip_tags($htmlBody));

            // send the email
            $mail->send();
        }

        // Perform association
        $a = new Association;
        $status = $a->doSaveWithValues($main_id_new, $linked_id_new, $type, $user_id);

        if (!$status)
        {
            return $this->ajax_feedback('Could not perform association');
        }
        
        // cache clearing for current doc in every lang:
        $this->clearCache($main_module, $main_id, false, 'view');
        $this->clearCache($linked_module, $linked_id, false, 'view');

        // html to return
        sfLoader::loadHelpers(array('Tag', 'Url', 'Asset', 'AutoComplete'));

        $linked_document->setBestName($user->getPreferedLanguageList());
        
        $bestname = $linked_document->get('name');
        if ($linked_module == 'routes')
        {
            // in that case, output not only route name but also best summit name whose id has been passed (summit_id)
            $summit = explode(' [',$this->getRequestParameter('summits_name'));
            $bestname = $summit[0] . $this->__('&nbsp;:') . ' ' . $bestname;
        }

        $linked_module_name = ($icon) ? $icon : $this->__($linked_module);
        $type_id_string = $type . '_' . $linked_id;
        
        $out = link_to($bestname, "@document_by_id?module=$linked_module&id=$linked_id");
        if ($user->hasCredential('moderator'))
        {
            $out .= c2c_link_to_delete_element($type, $main_id_new, $linked_id_new, !$swap, $strict);
        }
        
        if ($div)
        {
            $icon_string = '';
            if ($icon)
            {
                $icon_string = '<div class="assoc_img picto_' . $icon . '" title="' . ucfirst(__($icon)) . '">'
                             . '<span>' . ucfirst(__($icon)) . __('&nbsp;:') . '</span>'
                             . '</div>';
            }
            $out = '<div class="linked_elt" id="'.$type_id_string.'">'
                 . $icon_string
                 . $out
                 . '</div>';
        }
        else
        {
            $out = '<li id="' . $type_id_string . '">' 
                   . picto_tag('picto_' . $linked_module, $linked_module_name)
                   . ' ' . $out
                   . '</li>';
        }

        return $this->renderText($out);
    }


    /**
     * Executes remove document association
     */
    public function executeRemoveAssociation()
    {
        $user = $this->getUser();
        $user_id = $user->getId(); 
        $is_moderator = $user->hasCredential(sfConfig::get('app_credentials_moderator'));
        
        $type = $this->getRequestParameter('type');
        $main_id = $this->getRequestParameter('main_' . $type . '_id');
        $linked_id = $this->getRequestParameter('linked_id');
        $mode = $this->getRequestParameter('mode'); 
        $strict = $this->getRequestParameter('strict', 1); // whether 'remove action' should be strictly restrained to main and linked or reversed. 
        $icon = $this->getRequestParameter('icon');

        // if session is time-over
        if (!$user_id)
        {
            return $this->ajax_feedback('Session is over. Please login again.');
        }

        // only moderators can unlink documents
        if (!$is_moderator)
        {
            return $this->ajax_feedback('You do not have enough credentials to perform this operation');
        }


        // association cannot be created/deleted with self.
        if ($main_id == $linked_id)
        {
            return $this->ajax_feedback('A document can not be linked to itself');
        }
        
        // We check that this association type really exists 
        // for that, yaml is preferable over a db request, since all associations types are not allowed for quick associations
        if (!in_array($type, sfConfig::get('app_associations_types'))) 
        {
            return $this->ajax_feedback('Wrong association type');
        }
        
        $models = c2cTools::Type2Models($type);
        $main_model = $models['main'];
        $linked_model = $models['linked'];
        
        $main = Document::find($main_model, $main_id, array('id', 'module')); 
        if (!$main)
        {
            return $this->ajax_feedback('Document does not exist');
        }

        // check that linked doc exists: 
        // FIXME : combine request with main doc by looking only in documents table and check 'module' field is correct ?
        $linked = Document::find($linked_model, $linked_id, ($linked_model == 'Article') ? array('id', 'article_type') : array('id')); 
        if (!$linked)
        {
            return $this->ajax_feedback('Document does not exist');
        }

        $main_module = c2cTools::model2module($main_model);
        
        // check whether association has already been done or not
        $a = Association::find($main_id, $linked_id, $type, false); // false means not strict search (main and linked can be reversed)
        if ($a) 
        { 
            // check that user is moderator is done in security.yml

            // For a summit route association or a user outing association,
            // we must prevent the deletion of the last associated doc
            // For outings, we must check that at least one route or site will still be associated
            if ( (($type == 'sr' || $type == 'uo') && Association::countMains($linked_id, $type) == 1) ||
                 (($type == 'ro' || $type == 'to') && (Association::countMains($linked_id, array('ro', 'to')) == 1)) )
            {
                return $this->ajax_feedback('Operation forbidden: last association');
            }
            else
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
                    $al->is_creation = 'false';
                    $al->save();
            
                    $conn->commit();
                }
                catch (exception $e)
                {
                    $conn->rollback();
                    c2cTools::log("executeRemoveAssociation() : Association deletion + log failed ($main_id, $linked_id, $type, $user_id) - rollback");
                    return $this->ajax_feedback('Association deletion failed');
                }
            }
        }
        else
        {
            return $this->ajax_feedback('Operation not allowed');
        }
        
        // view action cache clearing (without whatsnew), since association is not logged in app_history_metadata and associations only appear on view:
        $this->clearCache($main_module, $main_id, false, 'view');
        $this->clearCache(c2cTools::model2module($linked_model), $linked_id, false, 'view');

        // for some cases (typically unlinking an image), we reload the doc
        // rather than removing a list entry
        if ($this->hasRequestParameter('reload'))
        {
            return $this->setNoticeAndRedirect('Image has been unlinked', $this->getRequest()->getReferer() . '#images');
        }
        else
        { 
            return $this->renderText('');
        }
    } 

    /**
     * Changes the association order for associations of same type (eg summit-summit)
     * It should only be used for ss, pp and tt associations, but there is probably
     * no real problem if other associations are inverted
     */
    public function executeInvertAssociation()
    {
        $user = $this->getUser();
        $user_id = $user->getId();
        $is_moderator = $user->hasCredential('moderator');

        $type = $this->getRequestParameter('type');
        $main_id = $this->getRequestParameter('main_id');
        $linked_id = $this->getRequestParameter('linked_id');

        // check if session timed out
        if (!$user_id)
        {
            return $this->setErrorAndRedirect('Session is over. Please login again.',
                                              $this->getRequest()->getReferer());
        }

        // only moderators can perform such actions
        if (!$is_moderator)
        {
            return $this->setErrorAndRedirect('You do not have enough credentials to perform this operation',
                                              $this->getRequest()->getReferer());
        }

        // we check that the association type really exists and that the two
        // documents are from the same model
        if ((substr($type, 0, 1) != substr($type, -1)) ||
            (!in_array($type, sfConfig::get('app_associations_types'))))
        {
            return $this->ajax_feedback('Wrong association type');
        }

        // check that association exists in database
        $models = c2cTools::Type2Models($type);
        $model = $models['main'];
        $module = c2cTools::model2module($model);

        $a = Association::find($main_id, $linked_id, $type); // strict search
        if (!$a)
        {
            return $this->setErrorAndRedirect('Operation not allowed', $this->getRequest()->getReferer());
        }

        // invert association
        $conn = sfDoctrine::Connection();
        try
        {
            $conn->beginTransaction();

            $a->main_id = $linked_id;
            $a->linked_id = $main_id;
            $a->save();

            $al1 = new AssociationLog();
            $al1->main_id = $main_id;
            $al1->linked_id = $linked_id;
            $al1->type = $type;
            $al1->user_id = $user_id;
            $al1->is_creation = 'false';
            $al1->save();

            $al1 = new AssociationLog();
            $al1->main_id = $linked_id;
            $al1->linked_id = $main_id;
            $al1->type = $type;
            $al1->user_id = $user_id;
            $al1->is_creation = 'true';
            $al1->save();

            $conn->commit();
        }
        catch (exception $e)
        {
            $conn->rollback();
            c2cTools::log("executeInvertAssociation() : invertion failed ($main_id, $linked_id, $type, $user_id) - rollback");
            return $this->ajax_feedback('Association invertion failed');
        }
         

        // remove cache and force page reload
        $this->clearCache($module, $main_id, false, 'view');
        $this->clearCache($module, $linked_id, false, 'view');
        return $this->setNoticeAndRedirect('Association inverted', $this->getRequest()->getReferer());
    }


    /**
     * Executes getautocomplete action.
     * returns a bit of html and JS to perform autocomplete
     */
    public function executeGetautocomplete()
    {
        sfLoader::loadHelpers(array('MyForm'));

        // retrieve module name on which to perform autocomplete
        if ($this->hasRequestParameter('module_id'))
        {
            $module_id = $this->getRequestParameter('module_id');
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
 
        $field_prefix = $this->getRequestParameter('field_prefix', '');

        sfLoader::loadHelpers(array('AutoComplete'));
        if ($module_name == 'users' && $this->getModuleName() == 'images'
            && !$this->getUser()->hasCredential('moderator')) // non-moderators can link images only to their profile
        {
            $user = $this->getUser();
            $out = input_hidden_tag('document_id', $user->getId(), array('id' => $field_prefix . '_document_id'))
                 . input_hidden_tag('document_module', $module_name, array('id' => $field_prefix . '_document_module'))
                 . $user->getUsername() . ' ' .  c2c_submit_tag(__('Link'), array('class' => 'samesize', 'picto' =>  'action_create'));
        }
        else if ($module_name != 'routes') // default case
        {
            $display_button = ($this->getRequestParameter('button') != '0');
            $out = input_hidden_tag('document_id', '0', array('id' => $field_prefix . '_document_id'))
                 . input_hidden_tag('document_module', $module_name, array('id' => $field_prefix . '_document_module'))
                 . c2c_auto_complete($module_name, $field_prefix.'_document_id', $field_prefix, '', $display_button)
                 . ($display_button ? '</form>' : '');
        }
        else
        {
            $summit_id = $field_prefix . '_summit_id';
            $div_select = $field_prefix . '_routes_select';
            $updated_failure = sfConfig::get('app_ajax_feedback_div_name_failure');

            $id = $field_prefix . '_rsummits_name';
            $out = input_hidden_tag('document_module', $module_name, array('id' => $field_prefix . '_document_module'))
                 . __('Summit : ')
                 . input_tag('summits_name', '', array('size' => 45, 'id' => $id))
                 . '<div id="'.$field_prefix.'_associated_routes" name="associated_routes" style="display:none;">'
                 . '<div id="' . $div_select . '" name="' . $div_select . '"></div>'

                 . javascript_queue("var indicator = $('#indicator');" .
                 "$('#$id').c2cAutocomplete({" .
                   "url: '" . url_for("summits/autocomplete") . "'," .
                   "minChars: " . sfConfig::get('app_autocomplete_min_chars') .
                 "}).on('itemselect', function(e, item) {" .
                   "indicator.show();" .
                   "$.get('" . url_for('summits/getroutes') . "'," .
                     "'summit_id=' + item.id + '&div_prefix=${field_prefix}_&div_name=document_id')" .
                     ".always(function() { indicator.hide(); })" .
                     ".fail(function(data) { C2C.showFailure(data.responseText); })" .
                     ".done(function(data) {" .
                       "$('#$div_select').html(data).parent().show();" .
                       "C2C.getWizardRouteRatings('${field_prefix}_document_id');" .
                     "});" .
                 "});");

            if ($this->getRequestParameter('button') != '0')
            {
                $out .= c2c_submit_tag(__('Link'), array('class' => 'samesize', 'picto' => 'action_create'));
            }
            $out .= '</div>';
        }
        
        return $this->renderText($out);
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
            case '=':
                return "$field=$value1";
            case ' ':
                return "$field= ";
            case '-':
                return "$field=-";
        }
    }
    
    protected function addCompareParam(&$out, $field)
    {
        $sel = $this->getRequestParameter($field . '_sel', null);
        $value = $this->getRequestParameter($field);
        if ($sel)
        {
            $out[] = self::makeCompareQueryString($field, $sel,
                                                  $value,
                                                  $this->getRequestParameter($field . '2'));
        }
        elseif (is_null($sel))
        {
            $out[] = "$field=$value";
        }
    }

    protected function addNameParam(&$out, $field)
    {
        if ($value = $this->getRequestParameter($field))
        {
            $value = str_replace('.', '%2E', $value);
            $out[] = $field . '=' . $value;
        }
    }

    protected function addListParam(&$out, $field, $rename = '', $default = '', $and_op = false)
    {
        $array = $this->getRequestParameter($field);
        if (empty($array) && !empty($default))
        {
            $array = $default;
        }
        
        if ($array)
        {
            if (is_array($array))
            {
                $has = $has_not = array();
                foreach ($array as $item)
                {
                    if (!empty($item))
                    {
                        if (strpos($item, '!') === false)
                        {
                            $has[] = $item;
                        }
                        else
                        {
                            $has_not[] = $item;
                        }
                    }
                }
                
                if (!$and_op)
                {
                    if (count($has_not))
                    {
                        $has_not = implode('', $has_not);
                        foreach ($has as $key => $item)
                        {
                            $has[$key] = $item . $has_not;
                        }
                    }
                    $out_temp = implode('-', $has);
                }
                else
                {
                    if (count($has_not))
                    {
                        $has_not = implode('', $has_not);
                        $has[] = $has_not;
                    }
                    $out_temp = implode(' ', $has);
                }
            }
            else
            {
                $out_temp = $array;
            }
            if ($out_temp == '_')
            {
                $out_temp = '-';
            }
            else
            {
                $out_temp = str_replace('_', '0', $out_temp);
            }
            if (!empty($out_temp))
            {
                if (!empty($rename))
                {
                    $field = $rename;
                }
                $out[] = $field . '=' . $out_temp;
            }
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
            else if ($sel == '=')
            {
                $out[] = "$field=$value1";
            }
            else
            {
                $out[] = "$field=-";
            }
        }
    }

    protected function addParam(&$out, $field, $alt_value = null)
    {
        $result = false;
        
        if ($value = $this->getRequestParameter($field))
        {
            $result = true;
        }
        else
        {
            $value = $alt_value;
        }
        
        if ($value)
        {
            if ($value == '_')
            {
                $value = '-';
            }
            $out[] = "$field=$value";
        }
        
        return $result;
    }

    // reconstruct an around param with the different field
    protected function addAroundParam(&$out, $field, $rename = '')
    {
        if ($sel = $this->getRequestParameter($field . '_sel'))
        {
            // param is ok if selector is != 0
            if ($sel != 0)
            {
                $lat = $this->getRequestParameter($field . '_lat');
                $lon = $this->getRequestParameter($field . '_lon');
                $range = round(1000 * (float) strtr($this->getRequestParameter($field . '_range'), ',', '.'));

                // check that our params are ok or discard
                if (!preg_match('/^-?\d*\.?\d+$/', $lat) || !preg_match('/^-?\d*\.?\d+$/', $lon)
                    || !preg_match('/^\d+$/', $range)) return;

                if (!empty($rename))
                {
                    $field = $rename;
                }
                $out[] = "$field=$lon,$lat,$range";
                
                return true;
            }
            
            return false;
        }
        
        return false;
    }

    protected function addDateParam(&$out, $field)
    {
        if ($sel = $this->getRequestParameter($field . '_sel'))
        {
            if ($sel == 4)
            {
                if ($date3 = $this->getRequestParameter($field . '3'))
                {
                    $out[] = "$field=$date3";
                }
            }
            else
            {
                if ($date1 = $this->getRequestParameter($field))
                {
                    $date_md = c2cTools::writeWith2Digits($date1['month'])
                             . c2cTools::writeWith2Digits($date1['day']);
                    $date1 = $date1['year'] . $date_md;
                }

                if ($date2 = $this->getRequestParameter($field . '2'))
                {
                    $date_md = c2cTools::writeWith2Digits($date2['month'])
                             . c2cTools::writeWith2Digits($date2['day']);
                    $date2 = $date2['year'] . $date_md;
                }
                
                $out[] = self::makeCompareQueryString($field, $sel, $date1, $date2);
            }
        }
    }

    // this function is used to build DB request from query formatted in HTML
    protected function buildCondition(&$conditions, &$values, $criteria_type, $field, $param, $join_id = null, $i18n = false, $extra = null)
    {
        $params_list = c2cTools::getCriteriaRequestParameters();
        
        Document::buildConditionItem($conditions, $values, $criteria_type, $field, $param, $join_id, $i18n, $params_list, $extra);
    }

    /**
     * Activities data is only available with some document types.
     */
    protected function getActivitiesList()
    {
        $list = sfConfig::get('app_activities_list');
        $activities = array();
        foreach($this->document->getActivities() as $act)
        {
            $activities[] = $this->__($list[$act]);
        }
        return implode(', ', $activities);
    }

    /**
     * Areas data is only available with some document types.
     */
    protected function getAreasList() // TODO order regions
    {
        $areas = array();
        $associated_areas = $this->associated_areas;
        foreach ($associated_areas as $key => $area)
        {
            $areas[] = $area['name'];
        }
        return implode(', ', $areas);
    }

    public function executeNext()
    {
        $this->redirectToPrevNext('next');
    }

    public function executePrev()
    {
        $this->redirectToPrevNext('prev');
    }

    protected function redirectToPrevNext($direction = 'next')
    {
        $current_id = $this->getRequestParameter('id');
        $document = new $this->model_class;
        $next_id = $document->getPrevNextId($this->model_class, $current_id, $direction);
        $module = $this->getModuleName();
        if (empty($next_id))
        {
            $next_id = $current_id;
            $this->setWarning('This document is already at the end of the list');
        }
        $this->redirect("@document_by_id?module=$module&id=$next_id", 301);
    }

    public function executeInsertimagetag()
    {
        $user = $this->getUser();
        $prefered_cultures = $user->getCulturesForDocuments();
        $module = $this->getRequestParameter('mod');
        $id = $this->getRequestParameter('id');
        $associated_docs = Association::findAllWithBestName($id, $prefered_cultures);
        $associated_images = Document::fetchAdditionalFieldsFor(
                                        array_filter($associated_docs, array('c2cTools', 'is_image')),
                                        'Image',
                                        array('filename', 'image_type'));
        $doc = Document::find(c2cTools::module2model($module), $id);
        if (empty($doc))
        {
            $this->setNotFoundAndRedirect();
        }
        if (c2cTools::is_collaborative_document($doc))
        {
          // for collaborative content, keep only collaborative images
          $associated_images = array_filter($associated_images, array('c2cTools', 'is_collaborative_document'));
        }
        $this->document_id = $id;
        $this->div = $this->getRequestParameter('div');
        $this->associated_images = $associated_images;
    }

    public function executeMap()
    {
        $this->debug = $this->getRequestParameter('debug');
    }

    public function executeCotometre()
    {
        // nothing
    }

    // generic get direction
    public function executeGetdirections()
    {
        sfLoader::loadHelpers(array('GetDirections'));

        $referer = $this->getRequest()->getReferer();
        $dest_id = $this->getRequestParameter('id');
        $service = $this->getRequestParameter('service');
        $user_id = $this->getUser()->getId();
        $lang = $this->getUser()->getCulture();

        // parking coords
        $dest_coords = Document::fetchAdditionalFieldsFor(array(array('id' => $dest_id)), 'Document', array('lat', 'lon'));

        if (empty($dest_coords) ||
            $dest_coords[0]['lat'] instanceOf Doctrine_Null ||
            $dest_coords[0]['lon'] instanceOf Doctrine_Null)
        {
            return $this->setWarningAndRedirect('Document does not exists or has no attached geometry', $referer);
        }

        // retrieve best parking name
        if ($service == 'gmaps' || $service == 'livesearch')
        {
            $name = urlencode(DocumentI18n::findBestName($dest_id, $this->getUser()->getCulturesForDocuments(), 'Document'));
        }

        // user coords - they can be either given as parameters, retrieved from user profile or inexistent
        $user_lon = floatval($this->getRequestParameter('lon'));
        $user_lat = floatval($this->getRequestParameter('lat'));
        if (!$user_lon || !$user_lat)
        {
            $user_coords = empty($user_id) ? null : Document::fetchAdditionalFieldsFor(array(array('id' => $user_id)), 'User', array('lat', 'lon'));

            if (empty($user_coords) ||
                $user_coords[0]['lat'] instanceOf Doctrine_Null ||
                $user_coords[0]['lon'] instanceOf Doctrine_Null)
            {
                $user_lat = $user_lon = null;
            }
            else
            {
                $user_lat = $user_coords[0]['lat'];
                $user_lon = $user_coords[0]['lon'];
            }
        }

        switch ($service)
        {
            case 'yahoo':
                 $url = yahoo_maps_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $lang);
                 break;
            case 'livesearch':
                 $url = live_search_maps_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $name);
                 break;
            case 'openmapquest':
                 $url = openmapquest_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $lang);
                 break;
            case 'osrm':
                 $url = osrm_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $lang);
                 break;
            case 'gmaps':
            default:
                 $url = gmaps_direction_link($user_lat, $user_lon, $dest_coords[0]['lat'], $dest_coords[0]['lon'], $lang);
                 break;
        }
        $this->redirect($url);
    }
    
    protected static function _getTooltipParamFromLayer($layer) {
        switch ($layer) {
            case 'public_transportations':
                $module = 'parkings';
                $type_where = 'public_transportation_rating IN (1,2,4,5)';
                break;
            case 'other_access':
                $module = 'parkings';
                $type_where = 'public_transportation_rating IS NULL OR public_transportation_rating NOT IN (1,2,4,5)';
                break;
            case 'admin_limits':
                $module = 'areas';
                $type_where = 'area_type=3';
                break;
            case 'countries':
                $module = 'areas';
                $type_where = 'area_type=2';
                break;
            case 'ranges':
                $module = 'areas';
                $type_where = 'area_type=1';
                break;
            default:
                $module = $layer;
                $type_where = null;
        }
        $model = c2cTools::module2model($module);
        return array($module, $model, $type_where);        
    }
    
    protected function setJsonResponse() {
        $this->setLayout(false);
        $response = $this->getResponse();
        $response->clearHttpHeaders();
        $response->setStatusCode(200);
        $response->setContentType('application/json; charset=utf-8');        
    }
    
    public function executeTooltip() {
        $bbox = $this->getRequestParameter('bbox');
        $layers = $this->getRequestParameter('layers');

        // TODO check params

        $this->items = array();

        foreach (explode(',', $layers) as $layer) {
            list($module, $model, $type_where) = self::_getTooltipParamFromLayer($layer);
            $where = gisQuery::getQueryByBbox($bbox, 'geom', $module);
            $q = Doctrine_Query::create()
                ->select('m.id, m.lat, m.lon, m.module')
                ->from("$model m")
                //->leftJoin("m.$model_i18n mi")
                ->where('m.redirects_to IS NULL')
                ->addWhere($where['where_string'])
                ->limit(10);
            if ($type_where) {
                $q->addWhere($type_where);
            }
            $res = $q->execute(array(), Doctrine::FETCH_ARRAY);
            if (count($res) > 0) {
                $this->items = array_merge($this->items, $res);
            }
        }

        $this->setJsonResponse();
    }
 
    public function executeTooltipPreview() {
        $module = $this->getRequestParameter('module');
        $id = $this->getRequestParameter('id');

        if ($module == 'users' && !$this->getUser()->isConnected() && !UserPrivateData::hasPublicProfile($id))
        {
            $this->name = $this->__('not available');
        }
        else
        {
            $model = c2cTools::module2model($module);
            $langs = sfContext::getInstance()->getUser()->getPreferedLanguageList();
            $i18n = Doctrine_Query::create()
                    ->select('m.culture, m.name')
                    ->from("${model}I18n m")
                    ->where('m.id = ?', array($id))
                    ->execute();
            $old_lang = 200;
            foreach($i18n as $name)
            {
                $lang_pos = array_search($name->get('culture'), $langs);
                if ($lang_pos === false) $lang_pos = 10;
                // test if language is prefered over the older
                if ($lang_pos < $old_lang)
                {
                    $old_lang = $lang_pos;
                    $uname = $name->get('name');
                }
            }
            $this->name = $uname;
        }

        $this->setJsonResponse();
    }
 
    public function executeTooltipTest() {
        $bbox = $this->getRequestParameter('bbox');
        $layers = $this->getRequestParameter('layers');

        // TODO check params

        $this->nb_items = 0;

        foreach (explode(',', $layers) as $layer) {
            list($module, $model, $type_where) = self::_getTooltipParamFromLayer($layer);
            $where = gisQuery::getQueryByBbox($bbox, 'geom', $module);
            $q = Doctrine_Query::create()
                ->from("$model m")
                ->where('m.redirects_to IS NULL')
                ->addWhere($where['where_string']);
            if ($type_where) {
                $q->addWhere($type_where);
            }
            //$this->nb_items += $q->count(); TODO is it quickier to use directly count?
            $results = $q->execute();
            $this->nb_items += count($results);
            // save information that can be useful in next steps
            if ($results->getFirst())
            {
                $sav_model = $model;
                $sav_id = $results->getFirst()->getId();
            }
        }

        // if only one result, directly display its name
        if ($this->nb_items == 1)
        {
            // specific behaviour for users: we don't want to display user name if profile
            // is private and user is not connected
            if ($sav_model == 'User' && !$this->getUser()->isConnected() && !UserPrivateData::hasPublicProfile($sav_id))
            {
                $this->name = $this->__('not available');
            }
            else
            {
                $langs = sfContext::getInstance()->getUser()->getPreferedLanguageList();
                $i18n = Doctrine_Query::create()
                        ->select('m.culture, m.name')
                        ->from("${sav_model}I18n m")
                        ->where('m.id = ?', array($sav_id))
                        ->execute();
                $old_lang = 200;
                foreach($i18n as $name)
                {
                    $lang_pos = array_search($name->get('culture'), $langs);
                    if ($lang_pos === false) $lang_pos = 10;
                    // test if language is prefered over the older
                    if ($lang_pos < $old_lang)
                    {
                        $old_lang = $lang_pos;
                        $uname = $name->get('name');
                    }
                }
                $this->name = $uname;
            }
        }

        $this->setJsonResponse();
    }

    public function executeCda()
    {
        $lang = $this->getRequestParameter('lang');
        if (!empty($lang))
        {
            $this->redirect('@cda');
        }
        
        $this->setTemplate('../../documents/templates/cda');
        $this->setLayout('layout_cda');
        $this->getResponse()->setTitle('changerdapproche.org');
        $this->getResponse()->addMeta('robots', 'index, follow');
    }

    public function executeCdasearch()
    {
        $ranges = $this->getAreas(1);
        $this->ranges = $ranges;

        $winter = sfConfig::get('app_cda_winter');
        $summer = sfConfig::get('app_cda_summer');
        $current_month = date('n', time());
        if ($current_month >= $summer['start'] && $current_month < $winter['start'])
        {
            $activities = array($summer['activity']);
        }
        else
        {
            $activities = array($winter['activity']);
        }
        $this->activities = $activities;
        
        $this->setTemplate('../../documents/templates/cdasearch');
        $this->setLayout('layout_cda');
        $this->getResponse()->setTitle('changerdapproche.org');
        $this->getResponse()->addMeta('robots', 'index, follow');
    }

    public function executeCdaredirect()
    {
        if ($this->getRequest()->getMethod() == sfRequest::POST)
        {
            $module = $this->getRequestParameter('module');
            $join = '';

            $criteria = array();
            
            // tc criteria
            if ($module == 'routes')
            {
                $criteria[] = 'tp=1-2-4-5';
            }
            elseif ($module == 'outings')
            {
                $criteria[] = 'owtp=1';
            }
            
            // area criteria
            $this->addListParam($criteria, 'areas');
            
            // activities criteria
            $this->addListParam($criteria, 'stags'); // for paragliding activity
            $activities = $this->getRequestParameter('act');
            $group_activities = $activities;
            if($key = array_search(8, $group_activities))
            {
                unset($group_activities[$key]);
            }
            $nb_grp = count($group_activities);
            $group_name = '';
            if ($nb_grp == 1)
            {
                $group_list = sfConfig::get('app_cda_activities_id');
                $group_name = $group_list[reset($group_activities)];
            }
            if ($group_name == 'crag')
            {
                $key = array_search(400, $activities);
                if ($module == 'outings')
                {
                    $activities[$key] = 4;
                }
                else
                {
                    unset($activities[$key]);
                }
            }
            
            if (count($activities))
            {
                $criteria[] = 'act=' . implode('-', $activities);
            }
            
            // module criteria
            if ($nb_grp == 1 && in_array($group_name, array('crag', 'climbing')))
            {
                if ($module == 'routes' && $group_name == 'crag')
                {
                    $module = 'sites';
                }
                if ($module == 'outings')
                {
                    if ($group_name == 'crag')
                    {
                        $join = 'sites';
                    }
                    else
                    {
                        $join = 'routes';
                    }
                }
            }
            
            // around criteria
            if ($module == 'routes')
            {
                $around = 'parnd';
            }
            elseif ($module == 'sites')
            {
                $around = 'tarnd';
            }
            elseif ($module == 'outings')
            {
                if ($nb_grp == 1 && $group_name == 'crag')
                {
                    $around = 'tarnd';
                }
                else
                {
                    $around = 'sarnd';
                }
            }
            $has_around = $this->addAroundParam($criteria, 'arnd', $around);
            if ($has_around)
            {
                $join = '';
            }
            
            // elevation critera
            if (!($nb_grp == 1 && $group_name == 'crag'))
            {
                $elevation = $this->getRequestParameter('elevation');
                if ($elevation >= 1 && $elevation <= 3)
                {
                    $elevation_range = sfConfig::get('app_cda_elevation_range');
                    $param = $elevation_range[$module . '_param'];
                    $value = $elevation_range[$elevation];
                    $criteria[] = "$param=$value";
                }
            }
            
            // difficulty criteria
            if (!($nb_grp == 1 && $group_name == 'crag' && $module == 'outings'))
            {
                $difficulty = $this->getRequestParameter('difficulty');
                
                if ($nb_grp == 1 && $difficulty >= 1 && $difficulty <= 3) // use difficulty criteria only if one group of activities is selected
                {
                    $group_info = sfConfig::get('app_cda_difficulty_range_' . $group_name);
                    $param = $group_info['param'];
                    $value = $group_info[$difficulty];
                    $criteria[] = "$param=$value";
                    $join = '';
                }
            }
            
            // join criteria
            if (!empty($join))
            {
                $criteria[] = "$join= ";
            }
            
            $route = '@default?module=' . $module . '&action=list&' . implode('&', $criteria);
            c2cTools::log("redirecting to $route");
            $this->redirect($route);
        }
        else
        {
            $this->setErrorAndRedirect('Invalid request parameters', '@cdasearch');
        }
    }
}
