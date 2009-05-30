<?php
/**
 * BaseDocument : This is the base model for documents
 * $Id: BaseDocument.class.php 2535 2007-12-19 18:26:27Z alex $
 */

class BaseDocument extends sfDoctrineRecordI18n
{
    /**
     * ORM settings
     */

    public function setTableDefinition()
    {
        $this->setTableName('documents');

        // ! important: doctrine adds _seq to the sequence name !
        // So dont add it here, but in the database
        $this->hasColumn('id', 'integer', 10, array('primary', 'seq' => 'documents_id'));
        $this->hasColumn('is_protected', 'boolean', null, array('default' => false));
        $this->hasColumn('redirects_to', 'integer', 10);
        $this->hasColumn('module', 'string', 20);
        $this->hasColumn('lon', 'double', null);
        $this->hasColumn('lat', 'double', null);
        $this->hasColumn('elevation', 'smallint', 4); 
        $this->hasColumn('geom_wkt', 'string', null); 
    }

    public function setUp()
    {
        $this->hasMany('DocumentI18n', array('local' => 'id', 'foreign' => 'id'));
        // commented because not compatible with hasMany('DocumentVersion as versions', array('local' => 'id' ... :
        //$this->hasMany('DocumentVersion as versions', array('local' => 'document_archive_id', 'foreign' => 'document_id'));
        $this->hasI18nTable('DocumentI18n', 'culture');

        // used for filtering 'recent' lists on associated regions (ranges):
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'id', 'foreign' => 'main_id'));  
        $this->hasMany('DocumentVersion as versions', array('local' => 'id', 'foreign' => 'document_id'));
    }

    /**
     * Common model tools
     */

    protected $is_archive = false,
              $is_preview = false,
              $is_available = false,
              $version,
              $metadatas;

    // omitted fields for diff displaying.
    // FIXME: customize it for every model, so that lon and lat of centroid do not appear on diff displaying in routes and outings after GPX import
    protected static $omitted_fields = array('id', 'user_id', 'is_minor', 'comment', 'parent_id',
                                             'culture', 'user_id_i18n', 'is_minor_i18n',
                                             'comment_i18n', 'created_at', 'created_at_i18n',
                                             'geom_wkt', 'module', 'is_protected', 'redirects_to',
                                             'v4_id', 'v4_app', 'v4_type', 'search_name'
                                             );
                                             
    
    public function getMetadatas()
    {
        return $this->metadatas;
    }

    public function setMetadatas($metadatas)
    {
        $this->metadatas = $metadatas;
    }

    public function isPreview()
    {
        return $this->is_preview;
    }

    public function setPreview()
    {
        $this->is_preview = true;
    }

    public function isArchive()
    {
        return $this->is_archive;
    }

    public function setArchive($version)
    {
        $this->is_archive = true;
        $this->setVersion($version);
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    protected function setFields($fields, $source_obj, $meta_obj)
    {
        foreach ($fields as $id => $name)
        {
            switch ($name)
            {
                case 'culture':
                    $this->setCulture($source_obj->get($name));
                    break;

                case 'user_id':
                case 'is_minor':
                case 'comment':
                case 'created_at':
                    $this->set($name, $meta_obj->get($name));
                    break;
                case 'id':
                    // TODO: find some method to set id ...
                    break;

                // dates must be converted into arrays
                case 'written_at':
                    $this->set($name, c2cTools::stringDateToArray($meta_obj->get($name)));
                    break;
                case 'date':
                    $this->set($name, c2cTools::stringDateToArray($source_obj->get($name)));
                    break;

                default:
                    $this->set($name, $source_obj->get($name));
            }
        }
    }

    /*
     * Hydrates an object whose id is given with all or only specified fields 
     *
     */
    public static function find($model, $id, $fields = null)
    {
        if (is_array($fields))
        {
            $select_string = 'd.'.implode(', d.', $fields);
            return Doctrine_Query::create()
                             ->select($select_string)
                             ->from($model.' d')
                             ->where('id = ?', array($id))
                             ->execute()
                             ->getFirst();
        }
        else
        {
            return sfDoctrine::getTable($model)->find($id);
        }
    }
    
    /*
     * Hydrates objects whose ids are given with all or only specified fields 
     *
     */
    public static function findIn($model, $ids, $fields = null)
    {
        if (!is_array($ids))
        {
            $ids = array($ids);
        }
        if (is_array($fields))
        {
            $select_string = 'd.' . implode(', d.', $fields);
            return Doctrine_Query::create()
                             ->select($select_string)
                             ->from($model.' d')
                             ->where('id IN (' . implode(',', $ids) . ')')
                             ->execute();
        }
        else
        {
            return sfDoctrine::getTable($model)->findByDql('id IN (' . implode(',', $ids) . ')');
        }
    }
    
    /**
     * Lists documents of current model taking into account search criteria or filters if any.
     * @return DoctrinePager
     */
    public static function browse($sort, $criteria)
    {
        $pager = self::createPager('Document', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
        
        // By default only name filter is used since 
        // it's the only one to apply to all types of documents.
        if (!empty($criteria))
        {
            $q->addWhere(implode(' AND ', $criteria[0]), $criteria[1]);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }

    /**
     * Sets base Doctrine pager object.
     */
    protected static function createPager($model, $select, $sort)
    {
        if (in_array($sort['order_by'], $select))
        {
            $order_by  = $sort['order_by'];
            $order_by .= (strtolower($sort['order']) == 'desc') ? ' DESC' : ' ASC';

            // specific behaviour when sorting by date, use id as second criterion
            if ($sort['order_by'] == 'm.date')
            {
                $order_by .= ', m.id';
                $order_by .= (strtolower($sort['order']) == 'desc') ? ' DESC' : ' ASC';
            }
        }
        else
        {
            $order_by = 'm.id DESC';
        }
        
        $model_i18n = $model . 'I18n';
        $pager = new c2cDoctrinePager($model, $sort['npp']);
        
        $q = $pager->getQuery();
        $q->select(implode(',', $select))
          ->from("$model m")
          ->leftJoin("m.$model_i18n mi")
          ->where('m.redirects_to IS NULL')
          ->orderBy($order_by);
        
        return $pager;
    }

    protected static function buildFieldsList()
    {
        return array('m.id', 'mi.culture', 'mi.name', 'mi.search_name', 'm.module');
    }

    protected static function buildGeoFieldsList()
    {
        return array('g.type', 'g.linked_id', 'ai.name', 'ai.search_name', 'm.geom_wkt');
    }

    protected static function filterOnLanguages($q)
    {
        self::filterOn('language', $q, 'mi');
    }

    protected static function filterOnActivities($q)
    {
        self::filterOn('activity', $q);
    }

    protected static function filterOnRegions($q)
    {
        self::filterOn('region', $q, 'g');
    }

    /**
     * Enable filters selected in customize action from user
     *
     * @param Doctrine_Query $query
     * @param String $alias
     * @return Doctrine_Query $query
     */
    public static function filterOn($preference, Doctrine_Query $query, $alias = null)
    {
        switch ($preference)
        {
            case 'activity' :
                $filtered_preferences  = c2cPersonalization::getInstance()->getActivitiesFilter();
                $log_msg               = 'filtering on activities';
                $query_string          = self::getActivitiesQueryString($filtered_preferences);
                break;
            case 'language' :
                $filtered_preferences  = c2cPersonalization::getInstance()->getLanguagesFilter();
                $log_msg               = 'filtering on languages';
                $query_string          = self::getLanguagesQueryString($filtered_preferences, $alias);
                break;
            case 'region' :
                $filtered_preferences  = c2cPersonalization::getInstance()->getPlacesFilter();
                $log_msg               = 'filtering on regions';
                $query_string          = self::getAreasQueryString($filtered_preferences, $alias); 
                break;
            default:
                $filtered_preferences  = array();
        }
                
        if (count($filtered_preferences) > 0)
        {
            c2cTools::log($log_msg);
            return $query->addWhere($query_string, $filtered_preferences);
        }

        return $query;
    }

    // this is for use with models which either need filtering on regions, or display of regions names.
    protected static function joinOnRegions($q)
    {
        $q->leftJoin('m.geoassociations g')
          ->leftJoin('g.AreaI18n ai');
    }

    public static function getActivitiesQueryString($activities)
    {
        $query_string = array();
        $query_string[] = 'activities IS NULL';
        foreach ($activities as $a)
        {
            $query_string[] = '? = ANY (activities)';
        }
        return implode($query_string, ' OR ');
    }

    public static function getLanguagesQueryString($langs, $alias = NULL)
    {
        $query_string = array();
        foreach ($langs as $l)
        {
            $query_string[] = '?';
        }
        return (!empty($alias) ? "$alias." : '') . 'culture IN (' .
               implode($query_string, ', ') . ' )';
    }

    public static function getAreasQueryString($areas, $alias = NULL)
    {
        $query_string = array();
        foreach ($areas as $a)
        {
            $query_string[] = '?';
        }
        return (!empty($alias) ? "$alias." : '') . 'linked_id IN (' .
               implode($query_string, ', ') . ' )';
        //' ) AND '.$alias.'.type = ?'; // this seems to cause additional computing time in postgres...
        //$filtered_preferences[] = 'dr'; // ranges
    }

    /**
     * Returns the best possible language for document reading,
     * taking into account :
     *  - an ordered array of prefered languages
     *  - the available cultures of the current document
     *
     * @param array $user_prefered_languages (user's prefered languages for document reading)
     * @return string culture
     */
    public function getBestCulture($user_prefered_languages)
    {
        $available_cultures = $this->getLanguages();

        switch(count($available_cultures))
        {
            case 0:
                return self::getDefaultCulture();

            case 1:
                return array_shift($available_cultures);

            default:
                // the document has many translations, so we get the best translation corresponding
                // to user's preferences
                foreach ($user_prefered_languages as $language)
                {
                    if (in_array($language, $available_cultures))
                    {
                        return $language;
                    }
                }
        }

        return self::getDefaultCulture();
    }

    /**
     * Gets default culture from config
     */
    protected static function getDefaultCulture()
    {
        return sfConfig::get('sf_i18n_default_culture');
    }

    /**
     * Fixme : is it still usefull ?
     * Sets the best possible language for document reading,
     * taking into account :
     *  - an ordered array of prefered languages
     *  - the available cultures of the current document
     *
     * @param array $user_prefered_languages (user's prefered languages for document reading)
     */
    public function setBestCulture($user_prefered_languages)
    {
        $this->setCulture($this->getBestCulture($user_prefered_languages));
    }


    /**
     * Sets the best possible name for document title,
     * taking into account :
     *  - an ordered array of prefered languages
     *
     * @param array $user_prefered_languages (user's prefered languages for document reading)
     */
    public function setBestName($user_prefered_languages)
    {
        $object_culture = $this->getCulture(); // what happens if object culture is not defined ?
        $this->setBestCulture($user_prefered_languages);
        $name = $this->get('name'); // FIXME: this hydrates the whole object, with all i18n fields
        $this->setCulture($object_culture);
        $this->set('name', $name); // we temporary set the name to its value in the best possible lang.
    }


    /**
     * Populates automatically an object
     * Select the default language defined in symfony or the user prefered language
     *
     * @param ResultSet $rs
     * @param integer $startcol
     * @return String the culture in wich to display document
     */
    public function getCulture()
    {
        // if document has no defined culture
        if (is_null($this->culture))
        {
            // we use an url parameter
            if ($lang = sfContext::getInstance()->getRequest()->getParameter('lang'))
            {
                $this->setCulture($lang);
            }
            else
            {
                // if user is authentified
                if(sfContext::getInstance()->getUser()->isConnected())
                {
                    // we use user preferences
                    $this->setCulture(sfContext::getInstance()->getUser()
                                               ->getPreferedLanguage());
                }
                else
                {
                    // we use the application default paramter
                    $this->setCulture(sfConfig::get('sf_i18n_default_culture'));
                }
            }
        }

        return $this->culture;
    }

    protected function getModelName()
    {
        return get_class($this);
    }

    protected function getI18nModelName()
    {
        return $this->getModelName() . 'I18n';
    }

    /**
     * Return true if the article is available (= has a description) in the selected
     * language and false else.
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->is_available;
    }

    public function setAvailable()
    {
        $this->is_available = true;
    }

    public function setNotAvailable()
    {
        $this->is_available = false;
    }

    /**
     * Return true if there is one or more translation
     * false else.
     *
     * @return boolean
     */
    public function hasTranslation()
    {
        return (count($this->get($this->getI18nModelName())) > 0);
    }

    /**
     * Get the available languages for the document.
     *
     * @return array
     */
    public function getLanguages()
    {
        $languages = array();
        foreach ($this->get($this->getI18nModelName()) as $i18n)
        {
            $culture = $i18n->getCulture();
            $languages[$culture] = $culture;
        }

        return $languages;
    }

    protected static function queryRecent($model, $user_id, $langs, $doc_id, $mode = 'editions', $ranges = null, $activities = null)
    {
        $query = array();
        $arguments = array();
        
        $langs = ($langs && !is_array($langs)) ? array($langs) : $langs;
        $ranges = ($ranges && !is_array($ranges)) ? array($ranges) : $ranges;
        $activities = ($activities && !is_array($activities)) ? array($activities) : $activities;
        
        if ($mode == 'creations')
        {
            $query[] = "d.version = ?";
            $arguments[] = 1;
        }

        if ($model != 'Document')
        {
            $query[] = "a.module = ?";
            $arguments[] = strtolower($model) . 's';
        }
        
        if (!empty($activities))
        {
            $subquery = array();
            foreach ($activities as $activity)
            {
                $subquery[] = '? = ANY (activities)';
                $arguments[] = $activity;
            }
            $query[] = '( ' . implode($subquery, ' OR ') . ' )';
        }

        if ($user_id)
        {
            $query[] = "h.user_id = ?";
            $arguments[] = $user_id;
        }

        if (!empty($langs))
        {
            $subquery = array();
            foreach ($langs as $lang)
            {
                $subquery[] = 'd.culture = ?';
                $arguments[] = $lang;
            }
            $query[] = '( ' . implode($subquery, ' OR ') . ' )';
        }
        
        if (!empty($ranges))
        {
            $subquery = array();
            foreach ($ranges as $range_id)
            {
                $subquery[] = 'g.linked_id = ?';
                $arguments[] = $range_id;
            }
            $query[] = '( ' . implode($subquery, ' OR ') . ' )';
            
            $query[] = 'g.type = ?';
            $arguments[] = 'dr'; // document_range association
        }

        if ($doc_id)
        {
            $query[] = "d.document_id = ?";
            $arguments[] = $doc_id;
        }

        $query = implode($query, ' AND ');

        return array('query' => $query, 'arguments' => $arguments);
    }

    /**
     * Retrieves a pager of recent changes eventually made by a specific user (documents new versions).
     * @param string model name
     * @return Pager
     */
    public static function listRecentChangesPager($model, $user_id = null, $lang = null, $doc_id = null)
    {
        // TODO: filter on lang, activities, regions
        $model_i18n = $model . 'I18n';

        $query_params = self::queryRecent($model, $user_id, $lang, $doc_id);

        $pager = new sfDoctrinePager($model, sfConfig::get('app_list_maxline_number', 25));

        $q = $pager->getQuery();
        $q->select('d.document_id, d.culture, d.version, d.nature, d.created_at, u.id, u.topo_name, i.name, a.module, h.comment, h.is_minor')
          ->from('DocumentVersion d')
          ->leftJoin('d.history_metadata h')
          ->leftJoin('h.user_private_data u')
          ->leftJoin('d.archive a')
          ->leftJoin('d.i18narchive i');

        if (!empty($query_params['query']))
        {
            $q->where($query_params['query'], $query_params['arguments']);
        }

        $q->orderBy('d.created_at DESC');

        return $pager;
    }

    /**
     * Retrieves a list of recent EDITIONS or CREATIONS (possibly made by a specific user).
     * @param string model name
     * @param integer max number of results
     * @return Document
     */
    public static function listRecent($model, $limit, $user_id = null, $langs = null, $doc_id = null,
                                      $mode = 'editions', $use_model_archives = false, $ranges = null,
                                      $whattoselect = null, $activities = null, $show_user = true)
    {
        $langs = ($langs && !is_array($langs)) ? array($langs) : $langs;
        $ranges = ($ranges && !is_array($ranges)) ? array($ranges) : $ranges;
        $activities = ($activities && !is_array($activities)) ? array($activities) : $activities;

        $query_params = self::queryRecent($model, $user_id, $langs, $doc_id, $mode, $ranges, $activities);

        $q = Doctrine_Query::create();
        
        if ($whattoselect)
        {
            $q->select($whattoselect); 
        }
        
        if ($use_model_archives)
        {
            $model_archive = $model . 'Archive';
            $model_i18n_archive = $model . 'I18nArchive';
        }
        else
        {
            $model_archive = 'archive';
            $model_i18n_archive = 'i18narchive';
        }
        
        $q->from('DocumentVersion d')
          ->leftJoin('d.history_metadata h')
          ->leftJoin('d.geoassociations g')
          ->leftJoin("d.$model_archive a")
          ->leftJoin("d.$model_i18n_archive i");
        if ($show_user)
        {
            $q->leftJoin('h.user_private_data u');
        }
       

        if (!empty($query_params['query']))
        {
            $q->where($query_params['query'], $query_params['arguments']);
        }

        $objects = $q->orderBy('d.created_at DESC')
                    ->limit($limit)
                    ->execute(array(), Doctrine::FETCH_ARRAY);

        return $objects;
    }

    /**
     * Returns the given document fields list without "meta" fields.
     */
    public static function getVisibleFieldNamesByObject(BaseDocument $document)
    {
        return self::getVisibleFieldNames($document->getTable(),
                                          $document->getI18nTable());
    }

    /**
     * Ditto than getVisibleFieldNamesByObject() except that it accepts a simple model name
     * as argument. Useful if no existing document object is available to test its fields.
     */
    public static function getVisibleFieldNamesByModel($model)
    {
        $model_i18n = $model . 'I18n';
        $document = new $model;
        $document_i18n = new $model_i18n;

        return self::getVisibleFieldNames($document->getTable(),
                                          $document_i18n->getTable());
    }

    /**
     * Common code of getVisibleFieldNamesByObject() and getVisibleFieldNamesByModel() methods.
     */
    protected static function getVisibleFieldNames($table, $i18n_table)
    {
        $fields = array_merge($table->getColumnNames(),
                              $i18n_table->getColumnNames());
        return array_diff($fields, self::$omitted_fields);
    }

    /**
     * Check if document exist in DB
     * @return True if document exist, false else
     */
    public static function checkExistence($model_class, $id)
    {
    	if ( ! is_array($id))
        {
            $id = array($id);
        }
        else
        {
            $id = array_values($id);
        }

        $doc = new $model_class;
        $model_class = $doc->getTable()->getTableName();
        $primary_key = $doc->getTable()->getPrimaryKeys();
        $selection = $model_class . '.' . $primary_key[0];

        $where  = $model_class . '.' . implode(' = ? AND .' .
                  $model_class, $primary_key) . ' = ?';

        $query = 'SELECT COUNT(' . $selection . ') AS nb ' .
                         ' FROM ' . $model_class .
                         ' WHERE ' . $where;

        $rs = sfDoctrine::connection()
                    ->standaloneQuery($query, $id)
                    ->fetchObject();

        $nb = ($rs->nb) ? $rs->nb : 0;

        return $nb > 0;
    }

    /**
     * Get the history of the document depending on the
     * current session language
     *
     * @param int $document_id
     * @return object document
     */
    public static function getHistoryFromId($document_id)
    {
        return self::getHistoryFromIdAndCulture($document_id, sfContext::getInstance()
                   ->getUser()
                   ->getPreferedLanguage());
    }

    public static function getHistoryFromIdAndCulture($document_id, $culture)
    {
        return Doctrine_Query::create()
                             ->from('DocumentVersion d ' .
                                    'LEFT JOIN d.archive ' .
                                    'LEFT JOIN d.i18narchive ' .
                                    'LEFT JOIN d.history_metadata h ' .
                                    'LEFT JOIN h.user_private_data u')
                             ->where('d.document_id = ? AND d.culture = ?',
                                     array($document_id, $culture))
                             ->orderBy('d.version desc')
                             ->execute(array(), Doctrine::FETCH_ARRAY);
    }

    /**
     * @param integer
     * @param string
     * @return integer
     */
    public static function getCurrentVersionNumberFromIdAndCulture($document_id, $culture)
    {
        $result = Doctrine_Query::create()
                             ->from('DocumentVersion d')
                             ->where('d.document_id = ? AND d.culture = ?',
                                     array($document_id, $culture))
                             ->orderBy('d.version desc')
                             ->limit(1)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
                             
        if (count($result))
        {
            return $result[0]['version'];
        }
        else
        {
            return 0;
        }
    }

    /**
     * Return a document built from given archive i18n-independent and i18n-dependent data.
     *
     * String is used to enable comparison between to document
     * Object is used to go back to a previous version (usage of correct id)
     * @param String_OR_Object $model_or_object
     */
    public static function createFromArchive($model_or_object, $data, $i18n_data, $metadata, $version_id)
    {
    	// if we pass a model to createFromArchive --> go back to a previous version
    	if(is_object($model_or_object))
        {
            $document = $model_or_object;
        }
        else // else it is a comparison between 2 documents
        {
        	$document = new $model_or_object;
        }

        // this is an archive
        $document->setArchive($version_id);

        // i18n setup...
        $document->setUp();
        $document->setCulture($i18n_data->getCulture());

        // get the document fields names
        $data_fields = $document->getTable()->getColumnNames();
        $i18n_fields = $document->getI18nTable()->getColumnNames();

        $document->setMetadatas($metadata);

        // set fields values
        $document->setFields($data_fields, $data, $metadata);
        $document->setFields($i18n_fields, $i18n_data, $metadata);

        $document->setAvailable();

        return $document;
    }

    /**
     * Retrieves data from an archived document version.
     */
    public static function getArchiveData($model, $model_i18n, $id, $lang, $version)
    {
        return Doctrine_Query::create()
                             ->from("$model a " .
                                    'LEFT JOIN a.document_version d ' .
                                    'LEFT JOIN d.history_metadata h ' .
                                    "LEFT JOIN d.$model_i18n " .
                                    'LEFT JOIN h.user_private_data u')
                             ->where('d.document_id = ? AND d.culture = ? AND d.version = ?',
                                     array($id, $lang, $version))
                             ->limit(1)
                             ->execute()
                             ->getFirst();
    }

    /**
     * Gets a list of documents filtering on the name field.
     * seems to be used only by autocomplete.
     */
    public static function searchByName($name, $model = 'Document', $user_id = 0)
    {
        $model_i18n = $model . 'I18n';
        
        $where_clause = "m.redirects_to IS NULL AND mi.search_name LIKE remove_accents(?)";
        
        if ($model == 'Outing')
        {
            // autocomplete on outings must only return those for which the current user is linked to.
            $results = Doctrine_Query::create()
                          ->select('mi.name, m.id, m.module, m.date')
                          ->from('Outing m , m.OutingI18n mi')
                          ->where($where_clause . " AND m.id IN (SELECT a.linked_id FROM Association a WHERE a.type = 'uo' AND a.main_id = ?)", 
                                    array('%' . $name . '%', $user_id))
                          ->orderBy('m.id DESC')
                          ->limit(sfConfig::get('app_list_maxline_number', 25))
                          ->execute(array(), Doctrine::FETCH_ARRAY);
        }
        else
        {
            $results = Doctrine_Query::create()
                          ->select('mi.name, m.id, m.module')
                          ->from($model . ' m , m.' . $model_i18n . ' mi')
                          ->where($where_clause, array('%' . $name . '%'))
                          ->limit(sfConfig::get('app_list_maxline_number', 25))
                          ->orderBy('m.id DESC')
                          ->execute(array(), Doctrine::FETCH_ARRAY);
        }

        return $results;
    }

    /**
     * Get a paged list of document filtering on the name field
     * Use it only in list templates
     *
     * @param String $name
     * @return sfDoctrinePager
     */
    public static function getListByName($name, $model = 'Document')
    {
        $model_i18n = $model . 'I18n';
        $selected_fields = 'm.id, m.module, mi.culture, mi.name, mi.search_name, m.geom_wkt';
        
        $pager = new sfDoctrinePager($model, sfConfig::get('app_list_maxline_number', 25));
        $q = $pager->getQuery();
        $q->select($selected_fields)
          ->from($model . ' m')
          ->leftJoin('m.' . $model_i18n . ' mi');
        
        $name = str_replace(array('   ', '  '), array(' ', ' '), $name);
        if ($model != 'Route')
        {
            $name = '%' . trim($name) . '%';
            $q->where('mi.search_name LIKE remove_accents(?) AND m.redirects_to IS NULL', array($name));
        }
        else
        {
            $name_list = explode(':', $name, 2);
            $summit_name = '%' . trim($name_list[0]) . '%';
            if (count($name_list) == 1)
            {
                $route_name = $summit_name;
                $condition_type = 'OR';
            }
            else
            {
                $route_name = '%' . trim($name_list[1]) . '%';
                $condition_type = 'AND';
            }
            $q->leftJoin('m.associations l')
              ->leftJoin('l.Summit s')
              ->leftJoin('s.SummitI18n si')
              ->addWhere("l.type = 'sr'")
              ->addWhere('((mi.search_name LIKE remove_accents(?) AND m.redirects_to IS NULL) ' . $condition_type . ' (si.search_name LIKE remove_accents(?)))', array($route_name, $summit_name));

        }
        
        return $pager;
    }

    /**
     * Deletes a document.
     */
    public static function doDelete($id)
    {
        $records = Doctrine_Query::create()
                             ->select('dv.documents_versions_id, dv.document_archive_id, dv.document_i18n_archive_id, dv.history_metadata_id')
                             ->from('DocumentVersion dv')
                             ->where('dv.document_id = ?', array($id))
                             ->execute();
        
        $da = array(); $dia = array(); $hm = array(); $dv = array();
        $question_marks = array();
        
        // build ids list for every table
        foreach ($records as $record)
        {
            $da[] = $record['document_archive_id'];
            $dia[] = $record['document_i18n_archive_id'];
            $hm[] = $record['history_metadata_id'];
            $dv[] = $record['documents_versions_id'];
            $question_marks[] = '?';
        }
        
        $question = implode(', ', $question_marks);

        $conn = sfDoctrine::Connection();
        try
        {
            $conn->beginTransaction();

            Doctrine_Query::create()->delete('DocumentVersion')
                                    ->from('DocumentVersion dv')
                                    ->where("dv.documents_versions_id IN ( $question )", $dv)
                                    ->execute();
                                        
            Doctrine_Query::create()->delete('DocumentArchive')
                                    ->from('DocumentArchive da')
                                    ->where("da.document_archive_id IN ( $question )", $da)
                                    ->execute();
                                        
            Doctrine_Query::create()->delete('DocumentI18nArchive')
                                    ->from('DocumentI18nArchive dia')
                                    ->where("dia.document_i18n_archive_id IN ( $question )", $dia)
                                    ->execute();
                
            Doctrine_Query::create()->delete('HistoryMetadata')
                                    ->from('HistoryMetadata hm')
                                    ->where("hm.history_metadata_id IN ( $question )", $hm)
                                    ->execute();
            $conn->commit();
        }
        catch (Exception $e)
        {
            $conn->rollback();
            throw $e;
        }
        return count($question_marks); // nb of deleted versions in DocumentVersion.
    }


    /**
     * Saves new data for the document with its metadata.
     *
     */
    public function doSaveWithMetadata($user_id, $is_minor = false, $comment = null)
    {
        $conn = sfDoctrine::Connection();
        try
        {
            $conn->beginTransaction();

            // history metadata saving here
            $history_metadata = new HistoryMetadata();
            $history_metadata->setComment($comment);
            $history_metadata->set('is_minor', $is_minor);
            $history_metadata->set('user_id', $user_id);

            // data saving must be done in this order :
            $history_metadata->save();
            $this->save();

            $conn->commit();
            
            return true;
        }
        catch (exception $e)
        {
            $conn->rollback();
            throw $e;
        }
    }

    /**  
     * Converts a 1-D PHP array into DB array notation.
     * Warning: PostgreSQL-dependent syntax
     * Warning 2: array entries must be integer since no quotes are added around them
     * @param array
     * @return string
     */
    public static function convertArrayToString($array)
    {
        if (empty($array))
        {
            return NULL;
        }

        return '{' . implode(',', $array) . '}';
    }

    public static function convertStringToArray($string)
    {
        if (is_array($string))
        {
            return $string;
        }

        if (empty($string))
        {
            return array();
        }

        $string = substr($string, 1, strlen($string) - 2); // removes {}
        return explode(',', $string);
    }

    /**  
     * Converts a multi-dimensional PHP array into DB array notation.
     * Warning: PostgreSQL-dependent syntax
     * Warning 2: array entries must be integer since no quotes are added around them
     * @param array
     * @return string
     */
    public static function convertMultiDimArrayToString($array)
    {
        if (empty($array) || !is_array($array))
        {
            return $array;
        }

        // case of multi-D arrays
        $array = array_map(array('self', 'convertArrayToString'), $array);

        return '{' . implode(',', $array) . '}';
    }

    public static function convertStringToMultiDimArray($string)
    {
        if (substr($string, 0, 1) != '{' || substr($string, -1) != '}')
        {
            return $string;
        }

        $string = substr($string, 1, strlen($string) - 2); 
        $array = explode(',', $string);
        
        return array_map(array('self', 'convertStringToArray'), $array);
    }

    public static function filterSetLat($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetLon($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    public static function returnNullIfEmpty($value)
    {
        if (empty($value))
        {
            return NULL;
        }
        return $value;
    }

    public static function returnPosIntOrNull($value)
    {
        if (is_numeric($value) && $value == (int)$value && $value > 0)
        {
            return $value;
        }

        return NULL;
    }

    /**
     * Retrieves the user (with id and name (the one to use) correctly hydrated) who created/uploaded this document
     * (specific to language version)
     */
    public function getI18nVersionCreator()
    {
        $result = Doctrine_Query::create()
                             ->select('dv.document_id, hm.user_id, u.topo_name')
                             ->from('DocumentVersion dv ' .
                                    'LEFT JOIN dv.history_metadata hm ' .
                                    'LEFT JOIN hm.user_private_data u')
                             ->where('dv.document_id = ? AND dv.culture = ? AND dv.version = ?',
                                     array($this->id, $this->getCulture(), 1))
                             ->limit(1)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
        
        if (isset($result[0]))
        {
            $u = $result[0]['history_metadata']['user_private_data'];
            $creator = array('id' => $result[0]['history_metadata']['user_id'], 'name' => $u['topo_name']);
        }
        else
        {
            $creator = array();
        }
        
        return $creator;
    }

    /**
     * Retrieves the user (with id and name (the one to use) correctly hydrated) who created/uploaded this document
     * (all language versions)
     */
    public function getCreator()
    {
        $result = Doctrine_Query::create()
                             ->select('dv.document_id, hm.user_id, u.topo_name')
                             ->from('DocumentVersion dv ' .
                                    'LEFT JOIN dv.history_metadata hm ' .
                                    'LEFT JOIN hm.user_private_data u')
                             ->where('dv.document_id = ? AND dv.version = ?',
                                     array($this->id, 1))
                             ->orderBy('dv.created_at ASC')
                             ->limit(1)
                             ->execute(array(), Doctrine::FETCH_ARRAY);

        if (isset($result[0]))
        {
            $u = $result[0]['history_metadata']['user_private_data'];
            $creator = array('id' => $result[0]['history_metadata']['user_id'], 'name' => $u['topo_name']);
        }
        else
        {
            $creator = array();
        }

        return $creator;
    }

    public static function fetchAdditionalFieldsFor($objects, $model, $fields)
    {   
        if (!count($objects)) 
        {
            return array();
        }
    
        $ids = array();
        $q = array();

        // build ids list
        foreach ($objects as $object)
        {
            $ids[] = $object['id'];
            $q[] = '?';
        }

        // db request fetching array with all requested fields
        $results = Doctrine_Query::create()
                          ->select('m.' . implode(', m.', $fields))
                          ->from("$model m")
                          ->where('m.id IN ( '. implode(', ', $q) .' )', $ids)
                          ->execute(array(), Doctrine::FETCH_ARRAY);

        $out = array();
        // merge array 'results' into array '$objects' on the basis of same 'id' key
        foreach ($objects as $object)
        {
            $id = $object['id'];
            foreach ($results as $result)
            {
                if ($result['id'] == $id)
                {
                    $out[] = array_merge($object, $result);
                }
            }
        }
        return $out;
    }

    public static function buildStringCondition(&$conditions, &$values, $field, $param)
    {
        $conditions[] = $field . ' LIKE remove_accents(?)';
        $values[] = '%' . urldecode($param) . '%';
    }
    public static function buildIstringCondition(&$conditions, &$values, $field, $param)
    {
        $conditions[] = $field . ' ILIKE ?';
        $values[] = '%' . urldecode($param) . '%';
    }
    public static function buildMstringCondition(&$conditions, &$values, $field, $param)
    {
        $param_list = explode(':', $param, 2);
        $summit_name = '%' . urldecode(trim($param_list[0])) . '%';
        if (count($param_list) == 1)
        {
            $route_name = $summit_name;
            $condition_type = 'OR';
        }
        else
        {
            $route_name = '%' . urldecode(trim($param_list[1])) . '%';
            $condition_type = 'AND';
        }
        $conditions[] = '((' . $field[0] . ' LIKE remove_accents(?) AND m.redirects_to IS NULL) ' . $condition_type . ' (' . $field[1] . ' LIKE remove_accents(?)))';
        $values[] = $route_name;
        $values[] = $summit_name;
    }

    public static function buildItemCondition(&$conditions, &$values, $field, $param)
    {
        $conditions[] = $field . ' = ?';
        $values[] = $param;
    }

    public static function buildMultiCondition(&$conditions, &$values, $field, $param)
    {
        $conditions[] = '? = ANY(' . $field . ')';
        $values[] = $param;
    }

    public static function buildCompareCondition(&$conditions, &$values, $field, $param)
    {
        if (!preg_match('/^(>|<|-)?([0-9]*)(~)?([0-9]*)$/', $param, $regs))
        {
            return;
        }

        if (!empty($regs[1]))
        {
            $compare = $regs[1];
        }
        elseif (!empty($regs[3]))
        {
            $compare = '~';
        }
        else
        {
            return;
        }

        if (!empty($regs[2]))
        {
            $value1 = $regs[2];
        }
        else if ($compare != '-')
        {
            return;
        }

        $value2 = !empty($regs[4]) ? $regs[4] : 0;

        switch ($compare) 
        {   
            case '>':
                $conditions[] = "$field >= ?";
                $values[] = $value1;
                break;

            case '<':
                $conditions[] = "$field <= ?";
                $values[] = $value1;
                break;

            case '~':
                $conditions[] = "$field BETWEEN ? AND ?";
                $values[] = min($value1, $value2);
                $values[] = max($value1, $value2);
                break;
            
            case '-':
                $conditions[] = "$field IS NULL";
        }
    }

    public static function buildListCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "$field IS NULL";
        }
        else
        {
            $items = explode('-', $param);
            $condition_array = array();
            $is_null = '';
            foreach ($items as $item)
            {
                if (strval($item) != '0')
                {
                    $condition_array[] = '?';
                    $values[] = $item;
                }
                else
                {
                    $is_null = " OR $field IS NULL";
                }
            }
            if (count($condition_array) == 1)
            {
                $condition = ' = ?';
            }
            else
            {
                $condition = ' IN ( ' . implode(', ', $condition_array) . ' )';
            }
            $conditions[] = $field . $condition . $is_null;
        }
    }

    public static function buildArrayCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "$field IS NULL";
        }
        else
        {
            $items = explode('-', $param);
            $condition_array = array();
            $cond = "? = ANY ($field)";
            $is_null = '';
            foreach ($items as $item)
            {
                if (strval($item) != '0')
                {
                    $condition_array[] = $cond;
                    $values[] = $item;
                }
                else
                {
                    $is_null = " OR $field IS NULL";
                }
            }
            $conditions[] = implode (' OR ', $condition_array) . $is_null;
        }
    }

    public static function buildBoolCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == 'yes')
        {
            $conditions[] = $field;
        }
        else
        {
            $conditions[] = $field . ' IS NOT TRUE';
        } 
    }

    public static function buildGeorefCondition(&$conditions, &$values, $field = 'm.geom_wkt', $param)
    {
        if (is_null($field))
        {
            $field = 'm.geom_wkt';
        }
        if ($param == 'yes')
        {
            $conditions[] = $field . ' IS NOT NULL';
        }
        else
        {
            $conditions[] = $field . ' IS NULL';
        } 
    }

    public static function buildFacingCondition(&$conditions, &$values, $field, $param)
    {
        $facings = explode('~', $param);
        if (count($facings) == 1)
        {
            if ($facings = '-')
            {
                $conditions[] = "$field IS NULL";
            }
            else
            {
                $conditions[] = "$field = ?";
                $values[] = $facings[0];
            }
        }
        else
        {
            $facing1 = $facings[0];
            $facing2 = $facings[1];
            
            if ($facing1 == $facing2)
            {
                $conditions[] = "$field = ?";
                $values[] = $facing1;
            }
            elseif ($facing1 > $facing2)
            {
                $conditions[] = "$field BETWEEN ? AND ?";
                $values[] = $facing2;
                $values[] = $facing1;
            }
            else
            {
                $conditions[] = "$field <= ? OR $field >= ?";
                $values[] = $facing1;
                $values[] = $facing2;
            }
        }
    }

    public static function buildBboxCondition(&$conditions, &$values, $field, $param)
    {
        $bbox_array = explode(',', $param);
        $reformatted_bbox = "$bbox_array[0] $bbox_array[1], $bbox_array[2] $bbox_array[3]";
        $reformatted_field = str_replace('.', '_', $field);
        $conditions[] = "get_bbox('$reformatted_field', '$reformatted_bbox')";
    }

    public static function listFromRegion($region_id, $buffer, $table = NULL, $where = '')
    {
        if (is_null($table)) $table = 'documents';
        $table_i18n = $table . '_i18n';
        $sql = "SELECT s.id, n.name, s.lon, s.lat, s.elevation FROM $table s, $table_i18n n " .
               "WHERE s.id = n.id AND s.redirects_to IS NULL AND s.geom IS NOT NULL AND n.culture = 'fr' $where " .
               "ORDER BY n.name ASC";
        // TODO: add filter on region
        return sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
    }

    /* produces a list of the last created docs */
    public static function getLastDocs()
    {
        /*
        $q = Doctrine_Query::create()
                             ->select('i.name, i.search_name, i.culture, a.id, a.module')
                             ->from('DocumentVersion d')
                             ->leftJoin('d.DocumentArchive a')
                             ->leftJoin('d.DocumentI18nArchive i')
                             ->where("d.version = 1 AND a.module != 'outings' AND a.module != 'users' AND a.module != 'images'")
                             ->limit(20)
                             ->orderBy('d.created_at DESC');
        //return $q->execute(array(), Doctrine::FETCH_ARRAY); // FIXME: returns nothing!?
        $sql = $q->getSql();
        */

        // following query is ok, but the displayed name is the first name given to the document, should be the last one
        /*
        $sql = 'SELECT a2.id AS id, a2.module AS module, a3.name AS name, a3.search_name AS search_name, a3.culture AS culture ' .
               'FROM app_documents_versions a ' .
               'LEFT JOIN app_documents_archives a2 ON a.document_archive_id = a2.document_archive_id ' .
               'LEFT JOIN app_documents_i18n_archives a3 ON a.document_i18n_archive_id = a3.document_i18n_archive_id ' .
               "WHERE (a.version = 1 AND a2.module != 'outings' AND a2.module !=  'users' AND a2.module != 'images' AND a2.module != 'articles') " .
               'ORDER BY a.created_at DESC LIMIT 20';*/

        // this one uses last document name
        $sql = 'SELECT sub.id AS id, sub.module AS module, a3.name AS name, a3.search_name AS search_name, a3.culture AS culture ' .
               'FROM ' .
               // start of subquery
               '(SELECT a2.id AS id, a2.module AS module, a.culture AS culture FROM app_documents_versions a ' .
               'LEFT JOIN app_documents_archives a2 ON a.document_archive_id = a2.document_archive_id ' .
               "WHERE (a.version = 1 AND a2.module != 'outings' AND a2.module !=  'users' AND a2.module != 'images' AND a2.module != 'articles') " .
               'ORDER BY a.created_at DESC LIMIT 20) AS sub ' .
               // end of subquery
               'LEFT JOIN documents_i18n a3 ON a3.id = sub.id AND sub.culture = a3.culture';

        $docs = sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();

        // get summit name for routes items
        $routes = Route::addBestSummitName(array_filter($docs, array('c2cTools', 'is_route')));
        foreach ($routes as $key => $route)
        {
            $docs[$key] = $route;
        }

        return $docs;
    }

    public function getPrevNextId($model, $current_id, $direction = 'next')
    {
        $where = 'm.id ' . ($direction == 'next' ? '<' : '>') . ' ?';
        $orderBy = 'm.id ' . ($direction == 'next' ? 'DESC' : 'ASC');
        $q = Doctrine_Query::create()
                           ->select('m.id')
                           ->from("$model m")
                           ->where($where, array($current_id))
                           ->addWhere('m.redirects_to IS NULL');
        
        if (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            $this->addPrevNextIdFilters($q, $model);
        }

        $res = $q->orderBy($orderBy)
                 ->limit(1)
                 ->execute()
                 ->getFirst();
        return !empty($res) ? $res->getId() : NULL;
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        // to be implemented in extended classes
    }

    protected static function joinOnI18n($q, $model)
    {
        $model_i18n = $model . 'I18n';
        $q->leftJoin("m.$model_i18n mi");
    }
}
