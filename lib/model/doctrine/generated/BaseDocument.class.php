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
                                             'v4_id', 'v4_app', 'v4_type', 'search_name', 'has_svg'
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

    // this function is used to build DB request from query formatted in HTML
    public static function buildConditionItem(&$conditions, &$values, $criteria_type, $field, $param, $join_id = null, $i18n = false, $params_list = array())
    {
        
        if (is_array($param))
        {
            list($param1, $param2) = $param;
            $value = c2cTools::getArrayElement($params_list, $param1, $param2);
        }
        else
        {
            $value = c2cTools::getArrayElement($params_list, $param);
        }
        
        if ($value)
        {
        /*    call_user_func_array
            (
                array('Document', 'build' . $criteria_type . 'Condition'),
                array($conditions, $values, $field, $value)
            );
            Don't work. Try another way...*/
            $nb_join = 1;
            
            switch ($criteria_type)
            {
                case 'String':  self::buildStringCondition(&$conditions, &$values, $field, $value); break;
                case 'Istring': self::buildIstringCondition(&$conditions, &$values, $field, $value); break;
                case 'Mstring': self::buildMstringCondition(&$conditions, &$values, $field, $value); break;
                case 'Item':    self::buildItemCondition(&$conditions, &$values, $field, $value); break;
                case 'Multi':   self::buildMultiCondition(&$conditions, &$values, $field, $value); break;
                case 'Compare': self::buildCompareCondition(&$conditions, &$values, $field, $value); break;
                case 'List':    self::buildListCondition(&$conditions, &$values, $field, $value); break;
                case 'Multilist': $nb_join = self::buildMultilistCondition(&$conditions, &$values, $field, $value); break;
                case 'Linkedlist': self::buildLinkedlistCondition(&$conditions, &$values, $field, $value); break;
                case 'Array':   self::buildArrayCondition(&$conditions, &$values, $field, $value); break;
                case 'Bool':    self::buildBoolCondition(&$conditions, &$values, $field, $value); break;
                case 'Georef':  self::buildGeorefCondition(&$conditions, &$values, $field, $value); break;
                case 'Facing':  self::buildFacingCondition(&$conditions, &$values, $field, $value); break;
                case 'Date':     self::buildDateCondition(&$conditions, &$values, $field, $value); break;
                case 'Bbox':    self::buildBboxCondition(&$conditions, &$values, $field, $value); break;
                case 'Around':    self::buildAroundCondition(&$conditions, &$values, $field, $value); break;
                case 'Config':    self::buildConfigCondition(&$conditions, &$values, $join_id, $value);
                    $join_id = '';
                    break;
                case 'Order': $nb_join = self::buildOrderCondition($value, $field); break;
            }
            
            if ($join_id && $nb_join)
            {
                if ($nb_join == 1)
                {
                    $conditions[$join_id] = true;
                }
                else
                {
                    $join_index = 1;
                    $join_id_index = $join_id;
                    while ($join_index <= $nb_join)
                    {
                        $conditions[$join_id_index] = true;
                        
                        $join_index += 1;
                        $join_id_index = $join_id . $join_index;
                    }
                }
                if ($i18n)
                {
                    $conditions[$join_id.'_i18n'] = true;
                }
            }
        }
    }

    public static function buildAreaCriteria(&$conditions, &$values, $params_list)
    {
        if (c2cTools::getArrayElement($params_list, 'areas'))
        {
            self::buildConditionItem($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area', false, $params_list);
        }
        elseif (c2cTools::getArrayElement($params_list, 'bbox'))
        {
            self::buildConditionItem($conditions, $values, 'Bbox', 'm.geom', 'bbox', null, false, $params_list);
        }
        elseif (c2cTools::getArrayElement($params_list, 'around'))
        {
            self::buildConditionItem($conditions, $values, 'Around', 'm.geom', 'around', null, false, $params_list);
        }
    }
    
    /**
     * Lists documents of current model taking into account search criteria or filters if any.
     * @return DoctrinePager
     */
    public static function browse($sort, $criteria, $format = null)
    {
        $field_list = self::buildFieldsList();
        $field_list[] = 'm.module';
        $pager = self::createPager('Document', $field_list, $sort);
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
        return array('m.id', 'mi.culture', 'mi.name');
    }

    protected static function buildGeoFieldsList()
    {
        return array('g0.type', 'g0.linked_id', 'ai.name', 'm.geom_wkt');
    }

    protected static function filterOnLanguages($q, $langs = null, $alias = 'mi')
    {
        if  (is_null($langs))
        {
            $langs = c2cPersonalization::getInstance()->getLanguagesFilter();
        }
        if (!empty($langs))
        {
            $q->addWhere(self::getLanguagesQueryString($langs, $alias), $langs);
            c2cTools::log('filtering on languages');
        }
    }

    protected static function filterOnActivities($q, $activities = null, $alias_1 = null, $alias_2 = null)
    {
        if  (is_null($activities))
        {
            $langs = c2cPersonalization::getInstance()->getActivitiesFilter();
        }
        if (!empty($activities))
        {
            $q->addWhere(self::getActivitiesQueryString($activities, $alias_1, $alias_2), $activities);
            c2cTools::log('filtering on activities');
        }
    }

    protected static function filterOnRegions($q, $areas = null, $alias = 'g2')
    {
        if  (is_null($areas))
        {
            $areas = c2cPersonalization::getInstance()->getPlacesFilter();
        }
        if (!empty($areas))
        {
            $q->leftJoin('m.geoassociations ' . $alias)
              ->addWhere(self::getAreasQueryString($areas, $alias), $areas);
            c2cTools::log('filtering on regions');
        }
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

    protected static function joinOnMulti($q, $conditions, $join_id, $join_class, $max_join = 10)
    {
        $join_index = 1;
        $join_id_index = $join_id;
        while(isset($conditions[$join_id_index]) && ($join_index <= $max_join))
        {
            $q->leftJoin($join_class . $join_index);
            unset($conditions[$join_id_index]);
            
            $join_index += 1;
            $join_id_index = $join_id . $join_index;
        }
        
        return $conditions;
    }

    // this is for use with models which either need filtering on regions, or display of regions names.
    protected static function joinOnRegions($q)
    {
        $q->leftJoin('m.geoassociations g0')
          ->leftJoin('g0.AreaI18n ai');
    }

    // this is for use with models which either need filtering on regions, or display of regions names.
    protected static function joinOnMultiRegions($q, $conditions)
    {
        return self::joinOnMulti($q, $conditions, 'join_area', 'm.geoassociations g', 3);
    }

    protected static function joinOnLinkedDocMultiRegions($q, $conditions, $types = array())
    {
        if (isset($conditions['join_area']))
        {
            $q->leftJoin('m.associations l');
            
            if (count($types))
            {
                $q->addWhere("l.type IN ('" . implode($types, "', '") . "')");
            }
            
            $conditions = Document::joinOnMulti($q, $conditions, 'join_area', 'l.MainGeoassociations g', 3);
        }
        return $conditions;
    }

    public static function getActivitiesQueryString($activities, $alias_1 = null, $alias_2 = null)
    {
        $field_1 = $field_2 = 'activities';
        if (!empty($alias_1))
        {
            $field_1 = "$alias_1.$field_1";
        }
        if (!empty($alias_2))
        {
            $field_2 = "$alias_2.$field_2";
        }
        $query_string = array();
        $query_string[] = $field_1 . ' IS NULL';
        foreach ($activities as $a)
        {
            $query_string[] = '? = ANY (' . $field_2 . ')';
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

    public static function getAreasQueryString($areas, $alias = null)
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
                $subquery[] = 'g0.linked_id = ?';
                $arguments[] = $range_id;
            }
            $query[] = '( ' . implode($subquery, ' OR ') . ' )';
            
            $query[] = 'g0.type = ?';
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
          ->leftJoin('d.geoassociations g0')
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
        if (!is_array($id))
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
        if (is_object($model_or_object))
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
     * Only used for autocomplete
     *
     * If name appears to be an integer, we use it as document id
     */
    public static function searchByName($name, $model = 'Document', $user_id = 0, $filter_personal_content = false, $exact_match = false)
    {
        $model_i18n = $model . 'I18n';
        $use_docid = (intval($name) > 1);
        $name = $use_docid ? intval($name) : $name;

        $operator = $exact_match ? '= ?' : "LIKE '%'||make_search_name(?)||'%'";
        $where_clause = $use_docid ? 'm.redirects_to IS NULL AND mi.id = ?'
                                   : 'm.redirects_to IS NULL AND mi.search_name ' . $operator;

        if ($model == 'Outing')
        {
            // autocomplete on outings must only return those for which the current user is linked to
            $select = 'mi.name, m.id, m.module, m.date';
            $where_clause = $where_clause . " AND m.id IN (SELECT a.linked_id FROM Association a WHERE a.type = 'uo' AND a.main_id = ?)";
            $where_vars = array($name, $user_id);
        }
        else if (($model == 'Article') && $filter_personal_content)
        {
            // return only collaborative articles, or personal ones linked with user
            $select = 'mi.name, m.id, m.module, m.article_type';
            $where_clause = $where_clause . " AND (m.article_type = 1 OR (m.id IN (SELECT a.linked_id FROM Association a WHERE a.type = 'uc' AND a.main_id = ?)))";
            $where_vars = array($name, $user_id);
        }
        else if (($model == 'Image') && $filter_personal_content)
        {
            // return only collaborative images or personal ones which were uploaded by user
            $select = 'mi.name, m.id, m.module, m.image_type';
            $where_clause = $where_clause . " AND (m.image_type = 1 OR (m.image_type = 2 AND m.id IN "
                                          . "(SELECT a.id FROM Image a LEFT JOIN a.versions v ON a.id = v.document_id "
                                          . "LEFT JOIN v.history_metadata a4 ON v.history_metadata_id = a4.history_metadata_id "
                                          . "WHERE a.redirects_to IS NULL AND (v.version = 1 AND a4.user_id = ?))))";
            $where_vars = array($name, $user_id);
        }
        else if ($model == 'User')
        {
            $select = 'mi.name, m.id, m.module, mu.username';
            $from = 'User m, m.UserI18n mi, m.private_data mu';
            if (!$use_docid)
            {
                $where_clause = 'm.redirects_to IS NULL AND (mi.search_name ' . $operator . ' OR mu.search_username ' . $operator . ')';
            }
            $where_vars = $use_docid ? array($name) : array($name, $name);
        }
        else
        {
            $select = 'mi.name, m.id, m.module';
            $where_vars = array($name);
        }

        $results = Doctrine_Query::create()
                                 ->select($select)
                                 ->from(isset($from) ? $from : $model.' m , m.'.$model_i18n.' mi')
                                 ->where($where_clause, $where_vars)
                                 ->limit(sfConfig::get('app_list_maxline_number', 25))
                                 ->orderBy('m.id DESC')
                                 ->execute(array(), Doctrine::FETCH_ARRAY);
 
        return $results;
    }

    /**
     * Same as above, but with some different behaviours for outings or routes
     */
    public static function quickSearchByName($name, $model = 'Document')
    {
        $model_i18n = $model . 'I18n';
        $selected_fields = 'm.id, m.module, mi.culture, mi.name';

        $q = Doctrine_Query::create()
             ->select($selected_fields)
             ->from($model . ' m')
             ->leftJoin('m.' . $model_i18n . ' mi');
        $name = str_replace(array('   ', '  '), array(' ', ' '), $name);

        if ($model == 'Route') // search routes based on the name of the route and the attached summits
        {
            $name_list = explode(':', $name, 2);
            $summit_name = trim($name_list[0]);
            if (count($name_list) == 1)
            {
                $route_name = $summit_name;
                $condition_type = 'OR';
            }
            else
            {
                $route_name = trim($name_list[1]);
                $condition_type = 'AND';
            }
            $q->leftJoin('m.associations l')
              ->leftJoin('l.Summit s')
              ->leftJoin('s.SummitI18n si')
              ->addSelect('m.activities')
              ->addWhere("l.type = 'sr'")
              ->addWhere('((mi.search_name LIKE \'%\'||make_search_name(?)||\'%\' AND m.redirects_to IS NULL) '
                             . $condition_type . ' (si.search_name LIKE \'%\'||make_search_name(?)||\'%\'))',
                         array($route_name, $summit_name));

        }
        else if ($model == 'User') // search topoguide or forum name
        {
            $name = trim($name);
            $q->addSelect('pd.username')
              ->leftJoin('m.private_data pd')
              ->addWhere('(mi.search_name LIKE \'%\'||make_search_name(?)||\'%\' OR pd.search_username LIKE \'%\'||make_search_name(?)||\'%\') AND m.redirects_to IS NULL', array($name, $name));
            if (!sfContext::getInstance()->getUser()->isConnected())
            {
                $q->addWhere('pd.is_profile_public = \'1\'');
            }
        }
        else
        {
            $name = trim($name);
            $q->where('mi.search_name LIKE \'%\'||make_search_name(?)||\'%\' AND m.redirects_to IS NULL', array($name));
        }
        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    /**
     * Deletes a document.
     * If lang is given, delete only the corresponding culture
     */
    public static function doDelete($id, $lang = null)
    {
        $query = Doctrine_Query::create()
                             ->select('dv.documents_versions_id, dv.document_archive_id, dv.document_i18n_archive_id, dv.history_metadata_id')
                             ->from('DocumentVersion dv')
                             ->where('dv.document_id = ?', array($id));
        if ($lang != null)
        {
            $query = $query->addWhere('dv.culture= ?', array($lang));
        }
        $records = $query->execute();
        
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

            if ($lang == null)
            {
                Doctrine_Query::create()->delete('DocumentArchive')
                                        ->from('DocumentArchive da')
                                        ->where("da.document_archive_id IN ( $question )", $da)
                                        ->execute();
            }

            Doctrine_Query::create()->delete('DocumentI18nArchive')
                                    ->from('DocumentI18nArchive dia')
                                    ->where("dia.document_i18n_archive_id IN ( $question )", $dia)
                                    ->execute();

            // some history metadata might be referenced by other docs/langs, we should not delete them (else, we get foreign key violation)
            $hms_to_keep = Doctrine_Query::create()->select('dv.history_metadata_id')
                                                  ->from('DocumentVersion dv')
                                                  ->where("dv.history_metadata_id IN ( $question )", $hm)
                                                  ->execute();

            $hm_k = array();
            foreach ($hms_to_keep as $hm_to_keep)
               $hm_k[] = $hm_to_keep['history_metadata_id'];

            $hm = array_diff($hm, $hm_k);
            if (count($hm))
            {
                $question = implode(', ', array_fill(0, count($hm), '?'));

                Doctrine_Query::create()->delete('HistoryMetadata')
                                        ->from('HistoryMetadata hm')
                                        ->where("hm.history_metadata_id IN ( $question )", $hm)
                                        ->execute();
            }

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

    // retrieves the creator of the document
    public static function getAssociatedCreatorData($objects)
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
                          ->select('v.document_id, hm.user_id, hm.written_at, u.topo_name')
                          ->from('DocumentVersion v')
                          ->leftJoin('v.history_metadata hm')
                          ->leftJoin('hm.user_private_data u')
                          ->where('v.document_id IN ( '. implode(', ', $q) .' )', $ids)
                          ->addWhere('v.version = 1')
                          ->execute(array(), Doctrine::FETCH_ARRAY);

        $out = array();
        // merge array 'results' into array '$objects' on the basis of same 'id' key
        foreach ($objects as $object)
        {
            $versions = array();
            $id = $object['id'];
            foreach ($results as $result)
            {
                if ($result['document_id'] == $id)
                {
                    $versions[] = $result;
                }
            }
            $object['versions'] = $versions;

            // get the first one that created the outing (whatever the culture) and grant him as author
            // smaller document version id = older one - because we have the creator for each linguistic version
            // TODO creation date is not lang dependent. Is it possible to do it?
            $documents_versions_id = null;
            foreach ($object['versions'] as $version)
            {
                if (!$documents_versions_id || $version['documents_versions_id'] < $documents_versions_id)
                {
                    $documents_versions_id = $version['documents_versions_id'];
                    $author_info_name = $version['history_metadata']['user_private_data']['topo_name'];
                    $author_info_id = $version['history_metadata']['user_private_data']['id'];
                    $date_info = $version['history_metadata']['written_at'];
                }
            }
            $object['creator'] = $author_info_name;
            $object['creator_id'] = $author_info_id;
            $object['creation_date'] = $date_info;

            $out[] = $object;
        }

        return $out;
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

    public static function addAssociatedDocuments(&$docs, $type, $is_main, $data_fields = array(), $i18n_fields = array())
    {
        if (count($docs) == 0 || (empty($data_fields) && empty($i18n_fields)))
        {
            return array();
        }

        $modules = c2cTools::Type2Modules($type);
        $main_module = $modules['main'];
        $linked_module = $modules['linked'];
        
        if (empty($main_module) || empty($linked_module))
        {
            return array();
        }

        $doc_ids = array();
        foreach (array_keys($docs) as $key)
        {
            $id = $docs[$key]['id'];
            $doc_ids[] = $id;
            $docs[$id] = $docs[$key];
            unset($docs[$key]);
        }

        // retrieve associations
        if ($main_module == $linked_module)
        {
            $associations = Association::countAll($doc_ids, $type);
            $module = $main_module;
        }
        elseif ($is_main)
        {
            $associations = Association::countAllLinked($doc_ids, $type);
            $module = $linked_module;
        }
        else
        {
            $associations = Association::countAllMain($doc_ids, $type);
            $module = $main_module;
        }

        if (count($associations) == 0) return array();
 
        $linked_ids = array();
        foreach ($associations as $assoc)
        {
            if ($is_main)
            {
                $doc_id = $assoc['main_id'];
                $linked_id = $assoc['linked_id'];
            }
            else
            {
                $doc_id = $assoc['linked_id'];
                $linked_id = $assoc['main_id'];
            }
            
            $linked_ids[] = $linked_id;
            if (isset($docs[$doc_id]['linked_docs']))
            {
                $docs[$doc_id]['linked_docs'] = array_merge($docs[$doc_id]['linked_docs'], array($linked_id));
            }
            else
            {
                $docs[$doc_id]['linked_docs'] = array($linked_id);
            }
        }
        $linked_ids = array_unique($linked_ids);
        $conditions = array();
        foreach ($linked_ids as $id)
        {
            $conditions[] = '?';
        }

        // retrieve info on the linked docs (name etc)
        $model = c2cTools::module2model($module);
        $q = Doctrine_Query::create()
             ->from("$model m")
             ->where('m.id IN ( '. implode(', ', $conditions) .' )', $linked_ids);
 
        if (!in_array('id', $data_fields))
        {
            $data_fields[] = 'id';
        }
        $q->addSelect('m.' . implode(', m.', $data_fields));
        
        $has_name = false;
        if (!empty($i18n_fields))
        {
            if (in_array('name', $i18n_fields))
            {
                if (!in_array('search_name', $i18n_fields))
                {
                    $i18n_fields[] = 'search_name';
                    $i18n_fields[] = 'culture';
                }
                $has_name = true;
            }
            $q->addSelect('mi.' . implode(', mi.', $i18n_fields))
              ->leftJoin('m.' . $model . 'I18n mi');
        }
 
        $linked_docs_info = $q->execute(array(), Doctrine::FETCH_ARRAY);
 
        if ($has_name)
        {
            $user_prefered_langs = sfContext::getInstance()->getUser()->getCulturesForDocuments();
            $linked_docs_info = Language::getTheBest($linked_docs_info, 'Parking');
        }

        // add linked docs info into $docs
        foreach ($docs as $doc_id => $doc)
        {
            if (isset($doc['linked_docs']))
            {
                foreach ($doc['linked_docs'] as $key => $linked_doc_id)
                {
                    $docs[$doc_id]['linked_docs'][$key] = $linked_docs_info[$linked_doc_id];
                }
            }
        }
    }


    public static function countAssociatedDocuments(&$docs, $type, $is_main)
    {
        if (count($docs) == 0)
        {
            return;
        }
        
        $doc_ids = array();
        foreach ($docs as $key => $doc)
        {
            $doc_ids[] = $doc['id'];
        }
        
        if ($is_main)
        {
            $associations = Association::countAllLinked($doc_ids, $type);
        }
        else
        {
            $associations = Association::countAllMain($doc_ids, $type);
        }

        if (count($associations) == 0) return;
        
        $linked_doc_count = array();
        foreach ($associations as $assoc)
        {
            if ($is_main)
            {
                $linked_doc_count[] = $assoc['main_id'];
            }
            else
            {
                $linked_doc_count[] = $assoc['linked_id'];
            }
        }
        
        $linked_doc_count = array_count_values($linked_doc_count);
        
        foreach ($docs as $key => $doc)
        {
            $doc_id = $doc['id'];
            if (isset($linked_doc_count[$doc_id]))
            {
                $docs[$key]['nb_linked_docs'] = $linked_doc_count[$doc_id];
            }
            else
            {
                $docs[$key]['nb_linked_docs'] = 0;
            }
        }
    }    
    
    public static function buildStringCondition(&$conditions, &$values, $field, $param)
    {
        $conditions[] = $field . ' LIKE \'%\'||make_search_name(?)||\'%\'';
        $values[] = urldecode($param);
    }
    public static function buildIstringCondition(&$conditions, &$values, $field, $param)
    {
        $conditions[] = $field . ' ILIKE ?';
        $values[] = '%' . urldecode($param) . '%';
    }
    /*
     * This function is used to search in 2 fields. If we got a :, first part is for first field,
     * second part for second field (and thus use AND)
     * Else we use OR on the two fields
     */
    public static function buildMstringCondition(&$conditions, &$values, $field, $param)
    {
        $param_list = explode(':', $param, 2);
        $first_name = urldecode(trim($param_list[0]));
        if (count($param_list) == 1)
        {
            $second_name = $first_name;
            $condition_type = 'OR';
        }
        else
        {
            $second_name = urldecode(trim($param_list[1]));
            $condition_type = 'AND';
        }
        $conditions[] = '((' . $field[0] . ' LIKE \'%\'||make_search_name(?)||\'%\' AND m.redirects_to IS NULL) '
                        . $condition_type . ' (' . $field[1] . ' LIKE \'%\'||make_search_name(?)||\'%\'))';
        $values[] = $second_name;
        $values[] = $first_name;
    }

    public static function buildItemCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "$field IS NULL";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL";
        }
        else
        {
            $conditions[] = $field . ' = ?';
            $values[] = $param;
        }
    }

    public static function buildMultiCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "$field IS NULL";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL";
        }
        else
        {
            $conditions[] = '? = ANY(' . $field . ')';
            $values[] = $param;
        }
    }

    public static function buildCompareCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "$field IS NULL";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL";
        }
        elseif (preg_match('/^(>|<)?([0-9]*)(~)?([0-9]*)$/', $param, $regs))
        {
            if (!empty($regs[1]))
            {
                $compare = $regs[1];
            }
            elseif (empty($regs[3]))
            {
                $compare = '=';
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
            else
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

                case '=':
                    $conditions[] = "$field = ?";
                    $values[] = $value1;
                    break;

                case '~':
                    $conditions[] = "$field BETWEEN ? AND ?";
                    $values[] = min($value1, $value2);
                    $values[] = max($value1, $value2);
                    break;
            }
        }
        else
        {
            return;
        }
    }

    public static function buildListCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "$field IS NULL";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL";
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

    public static function buildMultilistCondition(&$conditions, &$values, $field, $param)
    {
        if (($param == '-') || ($param == ' '))
        {
            $field_1 = $field[0] . '1.' . $field[1];
            if ($param == '-')
            {
                $conditions[] = "$field_1 IS NULL";
            }
            elseif ($param == ' ')
            {
                $conditions[] = "$field_1 IS NOT NULL";
            }
            
            return 1;
        }
        else
        {
            $item_groups = explode(' ', $param);
            $conditions_groups = array();
            $group_id = 0;
            foreach ($item_groups as $group)
            {
                $group_id += 1;
                $field_n = $field[0] . $group_id . '.' . $field[1];
                $items = explode('-', $group);
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
                        $is_null = " OR $field_n IS NULL";
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
                $conditions_groups[] = $field_n . $condition . $is_null;
                
            }
            
            $conditions[] = '(' . implode(') AND (', $conditions_groups) . ')';
            return $group_id;
        }
    }

    public static function buildLinkedlistCondition(&$conditions, &$values, $field, $param)
    {
        $field_0 = $field[0] . '.' . $field[1];
        $field_1 = $field[0] . '1.' . $field[1];
        $field_list = array(0 => $field_0, 1 => $field_1);
        
        if ($param == '-')
        {
            $conditions[] = "$field_1 IS NULL";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field_1 IS NOT NULL";
        }
        else
        {
            $items = explode('-', $param);
            $conditions_groups = array();
            $group_id = 0;
            for($group_id = 0; $group_id <= 1; $group_id++)
            {
                $field_n = $field_list[$group_id];
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
                        $is_null = " OR $field_n IS NULL";
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
                $linked_condition = '';
                if (($group_id == 1) && isset($field[2]))
                {
                    $linked_condition = ' AND ' . $field[2];
                }
                $conditions_groups[] = $field_n . $condition . $linked_condition . $is_null;
                
            }
            
            $conditions[] = '(' . implode(') OR (', $conditions_groups) . ')';
        }
    }

    public static function buildArrayCondition(&$conditions, &$values, $field, $param)
    {
        if (is_array($field))
        {
            $field_1 = $field[0] . '.' . $field[2];
            $field_2 = $field[1] . '.' . $field[2];
        }
        else
        {
            $field_1 = $field_2 = $field;
        }
        if ($param == '-')
        {
            $conditions[] = "$field_1 IS NULL";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field_1 IS NOT NULL";
        }
        else
        {
            $item_groups = explode('-', $param);
            $conditions_groups = array();
            $is_null = false;
            foreach ($item_groups as $group)
            {
                $items = explode(' ', $group);
                $condition_array = array();
                $cond = "(? = ANY ($field_2))";
                foreach ($items as $item)
                {
                    if (strval($item) != '0')
                    {
                        $condition_array[] = $cond;
                        $values[] = $item;
                    }
                    elseif (!$is_null)
                    {
                        $conditions_groups[] = "$field_1 IS NULL";
                        $is_null = true;
                    }
                }
                $conditions_groups[] = implode(' AND ', $condition_array);
            }
            $conditions[] = '((' . implode (') OR (', $conditions_groups) . '))';
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

    public static function buildConfigCondition(&$conditions, &$values, $join, $param)
    {
        if ($param == 'yes' || $param == '1')
        {
            $conditions[$join] = true;
        }
        elseif ($param == 'no' || $param == '0')
        {
            $conditions[$join] = false;
        }
        elseif (!empty($param))
        {
            $conditions[$join] = $param;
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
            elseif ($param == ' ')
            {
                $conditions[] = "$field IS NOT NULL";
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

    public static function buildDateCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "$field IS NULL";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL";
        }
        elseif (preg_match('/[YMWD]/', $param, $regs)) // 'since'
        {
            $pattern = array('Y', 'M', 'W', 'D');
            $replace = array(' years ', ' months ', ' weeks ', ' days ');
            $interval = str_replace($pattern, $replace, $param);
            $conditions[] = "age($field) < interval '$interval'";
        }
        elseif (preg_match('/^(>|<)?([0-9]*)(~)?([0-9]*)$/', $param, $regs))
        { // date comparison
            if (!empty($regs[1]))
            {
                $compare = $regs[1];
            }
            elseif (empty($regs[3]))
            {
                $compare = '=';
            }
            elseif (!empty($regs[3]))
            {
                $compare = '~';
            }
            else
            {
                return;
            }
            $value1 = $regs[2];
            $value2 = !empty($regs[4]) ? $regs[4] : 0;

            if (!empty($regs[3]) && strlen($regs[2]) != strlen($regs[4]))
            {
                return;
            }
            switch (strlen($regs[2]))
            {
                case 8: // YYYYMMDD
                    if (!checkdate(substr($regs[2],4,2), substr($regs[2],6,2), substr($regs[2],0,4)) ||
                        (!empty($regs[3]) &&  !checkdate(substr($regs[4],4,2), substr($regs[4],6,2), substr($regs[4],0,4))))
                    {
                        return;
                    }
                    else
                    {
                        self::buildCompareCondition($conditions, $values, $field, $param);
                    }
                    break;
                case 6: //YYYYMM
                    // TODO check input values
                    switch ($compare)
                    {
                        case '>':
                            $newparam = $compare . $value1 . '01';
                            break;
                        case '<':
                            // we need to provide a valid date
                            $year = substr($value1, 0, 4);
                            $month = substr($value1, 04, 2);
                            $day = self::getLastDay($year, $month);
                            $newparam = $compare . $value1 . $day;
                            break;
                        case '=':
                            // we need to provide a valid date
                            $year = substr($value1, 0, 4);
                            $month = substr($value1, 04, 2);
                            $day2 = self::getLastDay($year, $month);
                            $newparam = $value1 . '01~' . $value1 . $day2;
                            break;
                        case '~':
                            // we make sure that date1 < date2
                            $year2 = substr($value2, 0, 4);
                            $month2 = substr($value2, 04, 2);
                            $day2 = self::getLastDay($year2, $month2);
                            $newparam = min($value1, $value2) . '01' . $compare . max($value1, $value2) . $day2;
                            break;
                    }
                    self::buildCompareCondition($conditions, $values, $field, $newparam);
                    break;
                case 4: // MMDD
                    // TODO check input values
                    switch ($compare)
                    {
                        case '>':
                            $conditions[] = "date_part('month', date) > ? OR (date_part('month', date) = ? AND date_part('day', date) >= ?)";
                            $month = substr($value1, 0, 2);
                            $values[] = $month;
                            $values[] = $month;
                            $values[] = substr($value1, 2, 2);
                            break;
                        case '<':
                            $conditions[] = "date_part('month', date) < ? OR (date_part('month', date) = ? AND date_part('day', date) <= ?)";
                            $month = substr($value1, 0, 2);
                            $values[] = $month;
                            $values[] = $month;
                            $values[] = substr($value1, 2, 2);
                            break;
                        case '=':
                            $conditions[] = "date_part('month', date) = ? AND date_part('day', date) = ?";
                            $values[] = substr($value1, 0, 2);
                            $values[] = substr($value1, 2, 2);
                            break;
                        case '~': // youpi
                            if ($value1 <= $value2)
                            {
                                $conditions[] = "(date_part('month', date) > ? OR (date_part('month', date) = ? AND date_part('day', date) >= ?)) AND ".
                                                "date_part('month', date) < ? OR (date_part('month', date) = ? AND date_part('day', date) <= ?)";
                            }
                            else
                            {
                                $conditions[] = "(date_part('month', date) > ? OR (date_part('month', date) = ? AND date_part('day', date) >= ?)) OR ".
                                                "date_part('month', date) < ? OR (date_part('month', date) = ? AND date_part('day', date) <= ?)";
                            }
                            $month = substr($value1, 0, 2);
                            $day = substr($value1, 2, 2);
                            $values[] = $month;$values[] = $month;$values[] = $day;
                            $month = substr($value2, 0, 2);
                            $day = substr($value2, 2, 2);
                            $values[] = $month;$values[] = $month;$values[] = $day;
                            break;
                    }
                    break;
                case 2: // MM
                    if (((int)$regs[2] > 12 || (int)$regs[2] < 1) ||
                        (!empty($regs[3]) && ((int)$regs[4] > 12 || (int)$regs[4] < 1)))
                    {
                        return;
                    }
                    else
                    {
                        switch ($compare)
                        {
                            case '>':
                                $conditions[] = "date_part('month', date) >= ?";
                                $values[] = $value1;
                                break;
                            case '<':
                                $conditions[] = "date_part('month', date) <= ?";
                                $values[] = $value1;
                                break;
                            case '=':
                                $conditions[] = "date_part('month', date) = ?";
                                $values[] = $value1;
                                break;
                            case '~':
                                if ($value1 <= $value2) // like between july and august
                                {
                                    $conditions[] = "date_part('month', date) BETWEEN ? AND ?";
                                }
                                else // like between november and march
                                {
                                    $conditions[] = "(date_part('month', date) >= ? OR date_part('month', date) <= ?)";
                                }
                                $values[] = $value1;
                                $values[] = $value2;
                                break;
                        }
                    }
                    break;
                default:
                    return;
                    break;
            }
        }
        else
        {
            return;
        }
    }

    public static function buildBboxCondition(&$conditions, &$values, $field, $param)
    {
        /*
        $bbox_array = explode(',', $param);
        $reformatted_bbox = "$bbox_array[0] $bbox_array[1], $bbox_array[2] $bbox_array[3]";
        $reformatted_field = str_replace('.', '_', $field);
        $conditions[] = "get_bbox('$reformatted_field', '$reformatted_bbox')";
        */
        $param = str_replace(array('-', '~'), array(',', ','), $param);
        $where = gisQuery::getQueryByBbox($param);
        $conditions[] = $where['where_string'];
    }

    public static function buildAroundCondition(&$conditions, &$values, $field, $param)
    {
        $param = str_replace(array('-', '~'), array(',', ','), $param);
        $param = explode(',', $param);
        if (count($param) == 3)
        {
            self::buildXYCondition(&$conditions, &$values, $param[0], $param[1], $param[2]);
        }
    }

    public static function buildXYCondition(&$conditions, &$values, $x, $y, $tolerance)
    {
        $conditions[] = 'DISTANCE(SETSRID(MAKEPOINT(?,?), 900913), geom) < ?';
        array_push($values, $x, $y, round($tolerance));
    }

    public static function buildOrderCondition($param, $values)
    {
        if (in_array($param, $values))
        {
            return 1;
        }
        else
        {
            return 0;
        } 
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
    public static function getLastDocs($summit_separator = ': ')
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
               "WHERE (a.version = 1 AND a2.module NOT IN ('outings', 'users', 'images', 'articles') AND a2.redirects_to IS NULL) " .
               'ORDER BY a.created_at DESC LIMIT 20) AS sub ' .
               // end of subquery
               'LEFT JOIN documents_i18n a3 ON a3.id = sub.id AND sub.culture = a3.culture';

        $docs = sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();

        // get summit name for routes items
        $routes = Route::addBestSummitName(array_filter($docs, array('c2cTools', 'is_route')), $summit_separator);
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

    // given a year and a month, return the last day of the month
    protected static function getLastDay($year, $month)
    {
        if (checkdate($month, '31', $year))
        {
            return '31';
        }
        elseif (checkdate($month, '30', $year))
        {
            return '30';
        }
        elseif (checkdate($month, '29', $year))
        {
            return '29';
        }
        else
        {
            return '28';
        }
    }

    /*
     * Get column information (name, type)
     * @return array( name => array( type => integer/boolean..., ...) )
     */
    public static function getColumnsInfo($model_class)
    {
        $model = new $model_class;
        $table = $model->getTable();
        return $table->getColumns();
    }
}
