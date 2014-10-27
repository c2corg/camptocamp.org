s<?php
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
    
    public static function addParamForOrderby(&$params_list, $model)
    {
        $module = c2cTools::model2module($model);
        $sort_orderby_list = sfConfig::get('app_' . $module . '_filled_criteria');
        
        if (is_array($sort_orderby_list) && count($sort_orderby_list))
        {
            $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
            
            foreach ($orderby_list as $orderby)
            {
                if (!empty($orderby) && isset($sort_orderby_list[$orderby]))
                {
                    if (!empty($sort_orderby_list[$orderby]))
                    {
                        $orderby = $sort_orderby_list[$orderby];
                    }
                    if (!isset($params_list[$orderby]))
                    {
                        if (empty($params_list))
                        {
                            $params_list['perso'] = 'ifon';
                        }
                        $params_list[$orderby] = ' ';
                    }
                }
            }
        }
    }

    // this function is used to build DB request from query formatted in HTML
    public static function buildConditionItem(&$conditions, &$values, &$joins, &$params_list, $criteria_type, $field, $param, $join_ids = null, $extra = null)
    {
        if (empty($params_list))
        {
            return 0;
        }
        
        if (is_array($param))
        {
            list($param1, $param2) = $param;
            $value = c2cTools::getArrayElement($params_list, $param1, $param2);
        }
        else
        {
            $value = c2cTools::getArrayElement($params_list, $param);
        }
        
        if (!is_null($value))
        {
            $nb_join = 1;
            $result = true;
            $unset_param = true;
            
            if (is_array($join_ids) && !in_array($criteria_type, array('String', 'Mstring')))
            {
                $join_id = array_shift($join_ids);
            }
            else
            {
                $join_id = $join_ids;
            }
            
            switch ($criteria_type)
            {
                case 'String':
                    $infos = self::buildStringCondition($conditions, $values, $field, $value, $extra, $join_id);
                    if (!$infos['has_result'])
                    {
                        return 'no_result';
                    }
                    $result = $infos['nb_result'];
                    $join_id = $infos['join'];
                    $join_ids = null;
                    break;
                case 'Istring': self::buildIstringCondition($conditions, $values, $field, $value);
                    //$nb_join = 0;
                    break;
                case 'Mstring':
                    $infos = self::buildMstringCondition($conditions, $values, $field, $value, $extra, $join_id);
                    $result = array();
                    if ($infos === 'no_result')
                    {
                        return 'no_result';
                    }
                    $result = $infos[0]['nb_result'] + $infos[1]['nb_result'];
                    $join_ids = array();
                    if ($infos[0]['join'])
                    {
                        $join_ids[] = $infos[0]['join'];
                    }
                    if ($infos[1]['join'])
                    {
                        $join_ids[] = $infos[1]['join'];
                    }
                    $join_id = array_shift($join_ids);
                    break;
                case 'Item':    self::buildItemCondition($conditions, $values, $field, $value); break;
                case 'ItemNull':
                    $result = self::buildItemNullCondition($conditions, $values, $field, $value); break;
                case 'Multi':   self::buildMultiCondition($conditions, $values, $field, $value); break;
                case 'Compare': self::buildCompareCondition($conditions, $values, $field, $value); break;
                case 'Relative': self::buildRelativeCondition($conditions, $values, $field, $value); break;
                case 'List':
                    $use_not_null = ($param != 'id');
                    $result = self::buildListCondition($conditions, $values, $field, $value, $use_not_null); break;
                case 'Id':
                    $result = self::buildListCondition($conditions, $values, $field, $value, false);
                    if ($join_id && (($value == '-') || ($value == ' ') || ($result == 0)))
                    {
                        $joins[$join_id . '_has'] = true;
                    }
                    break;
                case 'MultiId':
                    $infos = self::buildMultiIdCondition($conditions, $values, $field, $value);
                    $nb_join = $infos['nb_group'];
                    $result = $infos['nb_id'];
                    if ($join_id && (($value == '-') || ($value == ' ') || ($nb_join > 0 && $result == 0)))
                    {
                        $joins[$join_id . '_has'] = true;
                    }
                    break;
                case 'Linkedlist': self::buildLinkedlistCondition($conditions, $values, $field, $value); break;
                case 'Array':   self::buildArrayCondition($conditions, $values, $field, $value); break;
                case 'Bool':    self::buildBoolCondition($conditions, $values, $field, $value); break;
                case 'Georef':  self::buildGeorefCondition($conditions, $values, $field, $value); break;
                case 'Facing':  self::buildFacingCondition($conditions, $values, $field, $value); break;
                case 'Date':     self::buildDateCondition($conditions, $values, $field, $value); break;
                case 'Bbox':    self::buildBboxCondition($conditions, $values, $field, $value); break;
                case 'Around':    self::buildAroundCondition($conditions, $values, $field, $value); break;
                case 'Config':    self::buildConfigCondition($joins, $join_id, $value);
                    $join_id = '';
                    break;
                case 'Join':    self::buildJoinCondition($joins, $values, $join_id, $value, $extra);
                    $join_id = '';
                    break;
            }
            
            if ($join_id && $nb_join)
            {
                if ($nb_join == 1)
                {
                    $joins[$join_id] = true;
                }
                else
                {
                    $join_index = 1;
                    $join_id_index = $join_id;
                    while ($join_index <= $nb_join)
                    {
                        $joins[$join_id_index] = true;
                        
                        $join_index += 1;
                        $join_id_index = $join_id . $join_index;
                    }
                }
                
                if (is_array($join_ids))
                {
                    foreach ($join_ids as $extra_join_id)
                    {
                        $joins[$extra_join_id] = true;
                    }
                }
            }
            
            if ($unset_param)
            {
                if (is_array($param))
                {
                    if (isset($params_list[$param1]))
                    {
                        unset($params_list[$param1]);
                    }
                    if (isset($params_list[$param2]))
                    {
                        unset($params_list[$param2]);
                    }
                }
                else
                {
                    unset($params_list[$param]);
                }
            }
        }
        else
        {
            $result = false;
        }
        
        return $result;
    }

    public static function buildPersoCriteria(&$conditions, &$values, &$joins, &$params_list, $module, $activity_param = 'act', $na_activities = array())
    {
        $has_merged = self::buildConditionItem($conditions, $values, $joins, $params_list, 'ItemNull', 'm.redirects_to', 'merged', 'merged');
        if ($has_merged)
        {
            $joins['merged'] = $has_merged;
        }
        else
        {
            $conditions[] = 'm.redirects_to IS NULL';
        }
        
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'Config', '', 'all', 'all');
        if (isset($joins['all']))
        {
            if ($joins['all'])
            {
                return;
            }
            else
            {
                unset($joins['all']);
            }
        }
        
        $perso = c2cTools::getArrayElement($params_list, 'perso');
        if (!empty($perso))
        {
            $perso = explode('-', $perso);
        }
        else
        {
            $perso = array();
        }
        
        $filters_active_and_on = c2cPersonalization::getInstance()->areFiltersActiveAndOn($module);
        
        if (!$has_merged)
        {
            if (   $filters_active_and_on
                && (   empty($params_list)
                    || (   count($perso) == 1
                        && in_array('ifon', $perso)))
            )
            {
                list($langs_enable, $areas_enable, $activities_enable) = c2cPersonalization::getDefaultFilters($module);
                if ($langs_enable) $perso[] = 'cult';
                if ($areas_enable) $perso[] = 'areas';
                if ($activities_enable) $perso[] = 'act';
            }
            elseif (!$filters_active_and_on && in_array('ifon', $perso))
            {
                $perso = array();
            }
        }
        else
        {
            $perso = array();
        }
        
        if (!empty($perso))
        {
            $params = array_keys($params_list);
            
            if (!array_intersect(array('areas', 'bbox', 'around'), $params) && array_intersect(array('areas', 'yes', 'all'), $perso))
            {
                
                $areas = c2cPersonalization::getInstance()->getPlacesFilter();
                if (count($areas))
                {
                    $params_list['areas'] = implode('-', $areas);
                }
            }
            
            if (!in_array($activity_param, $params) && array_intersect(array('act', 'yes', 'all'), $perso))
            {
                $activities = c2cPersonalization::getInstance()->getActivitiesFilter();
                $activities = array_udiff($activities, $na_activities, 'strcmp');
                if (count($activities))
                {
                    $params_list[$activity_param] = implode('-', $activities);
                }
            }
            
            $culture_param = c2cTools::Module2Letter($module) . 'cult';
            if (!in_array($culture_param, $params) && array_intersect(array('cult', 'yes', 'all'), $perso))
            {
                $cultures = c2cPersonalization::getInstance()->getLanguagesFilter();
                if (count($cultures))
                {
                    $params_list[$culture_param] = implode('-', $cultures);
                }
            }
            
            if (isset($params_list['perso']))
            {
                unset($params_list['perso']);
            }
            if (empty($params_list))
            {
                $joins['all'] = true;
            }
        }
        if (isset($params_list[$activity_param]))
        {
            $joins['act'] = $params_list[$activity_param];
        }
        else
        {
            $joins['act'] = '';
        }
    }

    public static function buildAreaCriteria(&$criteria, &$params_list, $m = 'm', $m2 = null, $join = null, $use_around = true)
    {
        $conditions = $values = $joins = $joins_order = array();
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('range'), 'range');
        self::buildOrderCondition($joins_order, $orderby_list, array('admin'), 'admin');
        self::buildOrderCondition($joins_order, $orderby_list, array('country'), 'country');
        self::buildOrderCondition($joins_order, $orderby_list, array('valley'), 'valley');
        
        $criteria[3] += $joins_order;
        
        if (empty($params_list))
        {
            return null;
        }
        
        if (c2cTools::getArrayElement($params_list, 'areas'))
        {
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', array('g', 'linked_id'), 'areas', 'area_id');
        }
        
        if (c2cTools::getArrayElement($params_list, 'bbox'))
        {
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Bbox', 'm.geom', 'bbox', null);
        }
        elseif ($use_around && c2cTools::getArrayElement($params_list, 'around'))
        {
            if (empty($m2))
            {
                $m2 = $m;
            }
            
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'around', $join);
        }
        
        if (!empty($conditions))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
        }
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
    }

    public static function buildListCriteria($params_list)
    {   
        $criteria = $conditions = $values = $joins = $joins_order = array();
        $criteria[0] = array(); // conditions
        $criteria[1] = array(); // values
        $criteria[2] = array(); // joins
        $criteria[3] = array(); // joins for order

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'documents');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('name'), array('document_i18n', 'join_document'));
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = $conditions;
            $criteria[1] = $values;
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // document criteria
        $m = 'm';
        $m2 = 'd';
        $midi18n = $mid;
        $join = null;
        $join_id = null;
        $join_idi18n = null;
        $join_i18n = 'document_i18n';
        
        $nb_id = 0;
        $nb_name = 0;
        
        $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'docs'), $join_id);
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
            }
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'darnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'di.search_name'), ($is_module ? array('dnam', 'name') : 'dnam'), array($join_idi18n, $join_i18n), 'Document');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Compare', $m . '.elevation', 'dalt', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'di.culture', 'dcult', $join_i18n);
        }
        
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
            
        
        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }
    
    /**
     * Lists documents of current model taking into account search criteria or filters if any.
     * @return DoctrinePager
     */
    public static function browse($model = 'Document', $sort, $criteria, $format = array(), $page = 1, $count = 0, $custom_fields = array())
    {
        if ($criteria === 'no_result')
        {
            return array('pager' => null,
                         'nb_results' => 0,
                         'query' => null);
        }
        
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        $joins_order = $criteria[3];
        
        $sub_query_result = self::browseId($model, $sort, $criteria, $format, $page, $count);
        
        $pager = $sub_query_result['pager'];
        $nb_results = $sub_query_result['nb_results'];
        $ids = $sub_query_result['ids'];
        
        if ($nb_results == 0)
        {
            return array('pager' => null,
                         'nb_results' => 0,
                         'query' => null,
                         'act' => null);
        }
        elseif ($nb_results == 1 && !array_intersect($format, array('cond', 'json', 'rss', 'widget')))
        {
            return array('pager' => null,
                         'nb_results' => 1,
                         'query' => null,
                         'id' => reset($ids),
                         'act' => $joins['act']);
        }
        
        $model_i18n = $model . 'I18n';
        $field_list = call_user_func(array($model, 'buildFieldsList'), true, 'mi', $format, $sort, $custom_fields);
        $where_ids = 'm.id' . $sub_query_result['where'];
        
        $q = Doctrine_Query::create();
        
        $q->select(implode(',', $field_list))
          ->from("$model m")
          ->leftJoin("m.$model_i18n mi")
          ->addWhere($where_ids, $ids);
        
        $model::buildMainPagerConditions($q, $criteria);
        
        if ($nb_results > 1)
        {
            if (count($joins_order))
            {
                $join = strtolower($model);
                
                $remove_join = $join . '_i18n';
                if (isset($joins_order[$remove_join]))
                {
                    unset($joins_order[$remove_join]);
                }
                if ($model == 'User' && isset($joins_order['user_pd']))
                {
                    unset($joins_order['user_pd']);
                }
                
                $remove_join = 'join_' . $join;
                if (count($joins_order) == 1 && isset($joins_order[$remove_join]))
                {
                    unset($joins_order[$remove_join]);
                }
                if (count($joins_order))
                {
                    $criteria[0] = array();
                    $criteria[1] = array();
                    $criteria[2] = $joins_order;
                    $model::buildPagerConditions($q, $criteria);
                }
            }
            $order_by = self::buildOrderby($field_list, $sort);
            $q->orderBy($order_by);
        }
        
        return array('pager' => $pager,
                     'nb_results' => $nb_results,
                     'query' => $q,
                     'act' => $joins['act']);
    }
    
    
    public static function browseId($model = 'Document', $sort, $criteria, $format = array(), $page = 1, $count = 0)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        $joins_order = $criteria[3];
        $joins_pager = $joins + $joins_order;
        $module = c2cTools::model2module($model);
        $mi = c2cTools::Model2Letter($model) . 'i';
        
        // orderby
        $sort = self::buildSortCriteria($model, $sort['orderby_params'], $sort['order_params'], $sort['npp'], $mi);
        
        // $npp
        $npp = $sort['npp'];
        
        // $all
        $all = false;
        if (isset($joins['all']))
        {
            $all = $joins['all'];
        }
        
        // $field_list
        $field_list = call_user_func(array($model, 'buildFieldsList'), false, $mi, $format, $sort);
        
        // specific $conditions
        if ($module == 'outings' && in_array('cond', $format))
        {
            $default_max_age = sfConfig::get('app_outings_recent_conditions_limit', '3W');
            $conditions[] = "age(date) < interval '$default_max_age'";
        }
        
        // $order_by
        $order_by = self::buildOrderby($field_list, $sort);
        
        // $nb_id
        $nb_id = 0;
        if (isset($joins['nb_id']))
        {
            $nb_id = $joins['nb_id'];
        }
        
        // $pager_count
        if ($nb_id > 0)
        {
            $count = $nb_id;
        }
        
        if ($count > $npp)
        {
            $pager_count = 0;
        }
        else
        {
            $pager_count = $count;
        }
        
        // $independant_count
        $independant_count = (!$pager_count && count($joins_pager) > count($joins));
        
        // create pager
        $pager = new c2cDoctrinePager($model, $npp, $pager_count, $independant_count);
        $pager->setPage($page);
        
        // independant count
        if ($independant_count)
        {
            $c = $pager->getCountQuery();
            $c->select('m.id')
              ->from("$model m");
            
            $model::buildPagerConditions ($c, $criteria);
        }
        
        // pager query
        $q = $pager->getQuery();
        $q->select(implode(',', $field_list))
          ->from("$model m")
          ->orderBy($order_by);
        
        if (!$all || !empty($joins_order))
        {
            $criteria[2] = $joins_pager;
            $model::buildPagerConditions ($q, $criteria);
        }
        else
        {
            $pager->simplifyCounter();
        }
        
        // execute pager query
        $pager->init();
        $count = $pager->getNbResults();
        if ($count == 0)
        {
            return array('pager' => null,
                         'nb_results' => 0,
                         'ids' => null);
        }
        
        // get ids
        $items = $pager->getResults('array');
        $ids = array();
        $where_ids = array();
        foreach ($items as $item)
        {
            $ids[] = $item['id'];
            $where_ids[] = '?';
        }
        $where_ids = implode(', ', $where_ids);
        $count_ids = count($ids);
        if ($count_ids == 1)
        {
            $where = ' = ' . $where_ids;
        }
        else
        {
            $where = ' IN ( ' . $where_ids . ' )';
        }
        
        return array('pager' => $pager,
                     'nb_results' => $count,
                     'ids' => $ids,
                     'where' => $where);
    }
    
    public static function buildMainPagerConditions(&$q, $criteria)
    {
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
    }

    /**
     * Sets base Doctrine pager object.
     */
    protected static function createPager($model, $select, $sort, $count = true)
    {
        $order_by = self::buildOrderby($select, $sort);
        
        $model_i18n = $model . 'I18n';
        $pager = new c2cDoctrinePager($model, $sort['npp'], $count);
        
        $q = $pager->getQuery();
        $q->select(implode(',', $select))
          ->from("$model m")
          ->leftJoin("m.$model_i18n mi")
          ->orderBy($order_by);
        
        return $pager;
    }

    /**
     * Build ORDERBY query parameter
     */
    protected static function buildOrderby($select, $sort)
    {
        $orderby_fields = $sort['orderby_fields'];
        $orders = $sort['orders'];
        
        if (count($orderby_fields) && count(array_intersect($orderby_fields, $select)) == count($orderby_fields))
        {
            $orderby = array();
            foreach ($orderby_fields as $key => $field)
            {
                $order = (strtolower($orders[$key]) == 'desc') ? ' DESC' : ' ASC';
                $orderby[] = $field . $order;
            }
            $orderby = implode(', ', $orderby);
        }
        else
        {
            $orderby = 'm.id DESC';
        }
        
        return $orderby;
    }

    /**
     * Detects list sort parameters: what field to order on, direction and 
     * number of items per page (npp).
     * @return array
     */
    public static function getListSortCriteria($model = 'Document', $default_npp = null, $max_npp = 100, $mi = 'mi')
    {
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        $order_list = c2cTools::getRequestParameterArray(array('order', 'order2', 'order3'), sfConfig::get('app_list_default_order'));
        
        if (empty($default_npp))
        {
            $default_npp = c2cTools::mobileVersion() ? sfConfig::get('app_list_mobile_maxline_number')
                                                     : sfConfig::get('app_list_maxline_number');
        }
        $npp = c2cTools::getRequestParameter('npp', $default_npp);
        if (!empty($max_npp))
        {
            $npp = min($npp, $max_npp);
        }
        
        return self::buildSortCriteria($model, $orderby_list, $order_list, $npp, $mi);
    }
    
    public static function buildSortCriteria($model, $orderby_list, $order_list, $npp, $mi = 'mi')
    {
        $sort_orderby_param = $sort_order_param = $sort_orderby_field = $extra_orderby_field = $sort_order = $extra_order = array();
        
        foreach ($orderby_list as $key => $orderby)
        {
            if (empty($orderby))
            {
                break;
            }
            
            $orderby_field = call_user_func(array($model, 'getSortField'), $orderby, $mi);
            if (is_null($orderby_field))
            {
                break;
            }
            $sort_orderby_param[] = $orderby;
            
            $order = $order_list[$key];
            $sort_order_param[] = $order;
            
            if (!is_array($orderby_field))
            {
                if (!in_array($orderby_field, $sort_orderby_field))
                {
                    $sort_orderby_field[] = $orderby_field;
                    $sort_order[] = $order;
                }
            }
            else
            {
                if (is_array($orderby_field[0]))
                {
                    $orderby_sublist = $orderby_field[0];
                    $order_sublist = $orderby_field[1];
                    foreach ($order_sublist as $key2 => $order2)
                    {
                        if (empty($order2))
                        {
                            $order_sublist[$key2] = $order;
                        }
                    }
                }
                else
                {
                    $orderby_sublist = $orderby_field;
                    $order_sublist = array_fill(0, count($orderby_sublist), $order);
                }
                
                $first_orderby_field = array_shift($orderby_sublist);
                if (!in_array($first_orderby_field, $sort_orderby_field))
                {
                    $sort_orderby_field[] = $first_orderby_field;
                    $sort_order[] = array_shift($order_sublist);
                    foreach($orderby_sublist as $key2 => $orderby2)
                    {
                        if (!in_array($orderby2, $extra_orderby_field))
                        {
                            $extra_orderby_field[] = $orderby2;
                            $extra_order[] = $order_sublist[$key2];
                        }
                    }
                }
            }
        }
        
        if (count($sort_orderby_field) == 1 && in_array($orderby_list[0], array('range', 'admin', 'country', 'valley')))
        {
            $extra_orderby_field[] = 'm.id';
            $extra_order[] = 'desc';
        }
        
        foreach($extra_orderby_field as $key2 => $orderby2)
        {
            if (!in_array($orderby2, $sort_orderby_field))
            {
                $sort_orderby_field[] = $orderby2;
                $sort_order[] = $extra_order[$key2];
            }
        }
        
        return array('orderby_params' => $sort_orderby_param,
                     'orderby_fields' => $sort_orderby_field,
                     'order_params'   => $sort_order_param,
                     'orders'         => $sort_order,
                     'npp'            => $npp
                    );
    }

    public static function getSortField($orderby, $mi = 'mi')
    {
        switch ($orderby)
        {
            case 'id':   return 'm.id';
            case 'name': return $mi . '.search_name';
            case 'module': return 'm.module';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {
        if ($main_query)
        {
            $data_fields_list = array('DISTINCT m.id', $mi . '.culture', $mi . '.name', 'm.module', $mi . '.search_name');
        }
        else
        {
            $data_fields_list = array('DISTINCT m.id');
        }
        
        $orderby_fields = array();
        if (isset($sort['orderby_fields']) && !empty($sort['orderby_fields']))
        {
            $orderby_fields = $sort['orderby_fields'];
            
            $orderby_params = $sort['orderby_params'];
            foreach ($orderby_params as $param)
            {
                switch ($param)
                {
                    case 'range': $orderby_fields[] = 'gr.type'; break;
                    case 'admin': $orderby_fields[] = 'gd.type'; break;
                    case 'country': $orderby_fields[] = 'gc.type'; break;
                    case 'valley': $orderby_fields[] = 'gv.type'; break;
                }
            }
        }
        
        return array_merge($data_fields_list, $orderby_fields);
    }

    protected static function buildGeoFieldsList()
    {
        return array('g0.type', 'g0.linked_id', 'ai.name', 'm.geom_wkt', 'ai.search_name');
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
            $activities = c2cPersonalization::getInstance()->getActivitiesFilter();
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

    protected static function joinOnMulti($q, &$joins, $join_id, $join_class, $max_join = 10)
    {
        $join_index = 1;
        $join_id_index = $join_id;
        while(isset($joins[$join_id_index]) && ($join_index <= $max_join))
        {
            $q->leftJoin($join_class . $join_index);
            
            $join_index += 1;
            $join_id_index = $join_id . $join_index;
        }
    }

    // this is for use with models which either need filtering on regions, or display of regions names.
    protected static function joinOnRegions($q)
    {
        $q->leftJoin('m.geoassociations g0')
          ->leftJoin('g0.AreaI18n ai');
    }

    // this is for use with models which either need filtering on regions, or display of regions names.
    protected static function buildAreaIdPagerConditions($q, &$joins)
    {
        self::joinOnMulti($q, $joins, 'area_id', 'm.geoassociations g', 3);
        
        if (isset($joins['range']))
        {
            $q->leftJoin('m.geoassociations gr')
              ->addWhere("gr.type = 'dr'");
        }
        if (isset($joins['admin']))
        {
            $q->leftJoin('m.geoassociations gd')
              ->addWhere("gd.type = 'dd'");
        }
        if (isset($joins['country']))
        {
            $q->leftJoin('m.geoassociations gc')
              ->addWhere("gc.type = 'dc'");
        }
        if (isset($joins['valley']))
        {
            $q->leftJoin('m.geoassociations gv')
              ->addWhere("gv.type = 'dv'");
        }
    }

    protected static function joinOnLinkedDocMultiRegions($q, &$joins, $types = array(), $use_main_geo_association = true, $join = 'area_id', $m = 'm', $l = 'l', $g = 'g')
    {
        if (isset($joins[$join]))
        {
            if ($m == 'm')
            {
                $q->leftJoin("$m.associations $l");
            }
            
            if (count($types))
            {
                $q->addWhere("$l.type IN ('" . implode($types, "', '") . "')");
            }
            
            if ($use_main_geo_association)
            {
                $geo_association = 'MainGeoassociations';
            }
            else
            {
                $geo_association = 'LinkedGeoassociations';
            }
            Document::joinOnMulti($q, $joins, $join, $l . '.' . $geo_association . ' ' . $g, 3);
        }
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
        // Uncomment to add "no activity" criteria
        // $query_string[] = $field_1 . ' IS NULL';
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

    protected static function queryRecent($mode = 'editions', $m, $mi, $langs = null, $areas = null, $activities = null, $doc_ids = null, $user_id = null, $editedby_id = null, $createdby_id = null, $params = array())
    {
        $query = array("dv.culture = $mi.culture");
        $arguments = array();
        
        $langs = ($langs && !is_array($langs)) ? explode('-', $langs) : $langs;
        $areas = ($areas && !is_array($areas)) ? explode('-', $areas) : $areas;
        $activities = ($activities && !is_array($activities)) ? explode('-', $activities) : $activities;
        $doc_ids = ($doc_ids && !is_array($doc_ids)) ? explode('-', $doc_ids) : $doc_ids;
        
        if ($mode == 'creations')
        {
            $query[] = 'dv.version = ?';
            $arguments[] = 1;
        }
        
        if (!empty($activities))
        {
            $subquery = array();
            foreach ($activities as $activity)
            {
                $subquery[] = "? = ANY ($m.activities)";
                $arguments[] = $activity;
            }
            $query[] = '( ' . implode($subquery, ' OR ') . ' )';
        }

        if ($user_id)
        {
            $query[] = "hm.user_id = ?";
            $arguments[] = $user_id;
        }

        if ($editedby_id)
        {
            $query[] = "hm2.user_id = ?";
            $arguments[] = $editedby_id;
        }

        if ($createdby_id)
        {
            $query[] = "hm3.user_id = ?";
            $arguments[] = $createdby_id;
            $query[] = 'dv3.version = ?';
            $arguments[] = 1;
        }

        if (!empty($langs))
        {
            $subquery = array();
            foreach ($langs as $lang)
            {
                $subquery[] = 'dv.culture = ?';
                $arguments[] = $lang;
            }
            $query[] = '( ' . implode($subquery, ' OR ') . ' )';
        }
        
        if (!empty($areas))
        {
            $subquery = array();
            foreach ($areas as $area)
            {
                $subquery[] = 'geo.linked_id = ?';
                $arguments[] = $area;
            }
            $query[] = ' ( ' . implode($subquery, ' OR ') . ' )';
        }
        
        if (!empty($doc_ids))
        {
            $subquery = array();
            foreach ($doc_ids as $doc_id)
            {
                $subquery[] = 'dv.document_id = ?';
                $arguments[] = $doc_id;
            }
            $query[] = ' ( ' . implode($subquery, ' OR ') . ' )';
        }
        
        if (!empty($params['ctyp']))
        {
            $query[] = "$m.article_type = ?";
            $arguments[] = $params['ctyp'];
        }
        
        if (!empty($params['ityp']))
        {
            $query[] = "$m.image_type = ?";
            $arguments[] = $params['ityp'];
        }

        $query = implode($query, ' AND ');

        return array('query' => $query, 'arguments' => $arguments);
    }

    /**
     * Retrieves a pager of recent changes eventually made by a specific user (documents new versions).
     * @param string model name
     * @return Pager
     */
    public static function listRecentChangesPager($model, $langs = null, $areas = null, $activities = null, $doc_ids = null, $user_id = null, $editedby_id = null, $createdby_id = null, $mode = 'editions', $params = array())
    {
        $m = strtolower(substr($model, 0, 1));
        $mi = $m . 'i';
        if ($model == 'Article')
        {
            $m .= '5';
        }
        else
        {
            $m .= '2';
        }
        
        $model_i18n = $model . 'I18n';

        $query_params = self::queryRecent($mode, $m, $mi, $langs, $areas, $activities, $doc_ids, $user_id, $editedby_id, $createdby_id, $params);
        
        $field_list = "dv.document_id, dv.culture, dv.version, dv.nature, dv.created_at, up.id, up.topo_name, $mi.name, hm.comment, hm.is_minor";
        if ($model == 'Document')
        {
            $field_list .= ", $m.module";
        }

        $pager = new c2cDoctrinePager($model, sfConfig::get('app_list_maxline_number', 25));

        $q = $pager->getQuery();
        $q->select($field_list)
          ->from('DocumentVersion dv')
          ->leftJoin('dv.history_metadata hm')
          ->leftJoin('hm.user_private_data up')
          ->innerJoin("dv.$model_i18n $mi");
        
        if ($model == 'Document' || !empty($activities) || !empty($params['ctyp']) || !empty($params['ityp']))
        {
            $q->innerJoin("dv.$model $m");
        }

        if (!empty($areas))
        {
            $q->leftJoin('dv.geoassociations geo');
        }

        if (!empty($editedby_id))
        {
            $q->leftJoin('dv.versions dv2')
              ->leftJoin('dv2.history_metadata hm2');
        }

        if (!empty($createdby_id))
        {
            $q->leftJoin('dv.versions dv3')
              ->leftJoin('dv3.history_metadata hm3');
        }
        
        if (!empty($query_params['query']))
        {
            $q->where($query_params['query'], $query_params['arguments']);
        }
        else
        {
            $pager->simplifyBaseCounter();
        }

        $q->orderBy('dv.created_at DESC');

        return $pager;
    }

    /**
     * Retrieves a list of recent EDITIONS or CREATIONS (possibly made by a specific user).
     * @param string model name
     * @param integer max number of results
     * @return Document
     */
    public static function listRecent($model, $limit, $user_id = null, $langs = null, $doc_id = null,
                                      $mode = 'editions', $ranges = null,
                                      $whattoselect = null, $activities = null, $show_user = true)
    {
        $m = strtolower(substr($model, 0, 1));
        $mi = $m . 'i';
        if ($model == 'Article')
        {
            $m .= '4';
        }
        else
        {
            $m .= '2';
        }
        
        $model_i18n = $model . 'I18n';
        
        $query_params = self::queryRecent($mode, $m, $mi, $langs, $ranges, $activities, $doc_id, $user_id);

        $q = Doctrine_Query::create();
        
        if ($whattoselect)
        {
            $field_list = $whattoselect; 
        }
        else
        {
            $field_list = "dv.document_id, dv.culture, dv.version, dv.created_at, $mi.name, $mi.search_name, $m.module, $m.lon, $m.lat, hm.comment";
            if ($show_user)
            {
                $field_list .= ', up.id, up.topo_name';
            }
        }
        
        $q->select($field_list)
          ->from('DocumentVersion dv')
          ->leftJoin('dv.history_metadata hm')
          ->innerJoin("dv.$model_i18n $mi")
          ->innerJoin("dv.$model $m");
        
        if ($ranges)
        {
            $q->leftJoin('dv.geoassociations geo');
        }
        
        if ($show_user)
        {
            $q->leftJoin('hm.user_private_data up');
        }
       

        if (!empty($query_params['query']))
        {
            $q->where($query_params['query'], $query_params['arguments']);
        }

        $objects = $q->orderBy('dv.created_at DESC')
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
     * Get the history of the document depending on the
     * current session language
     *
     * @param int $document_id
     * @return object document
     */
    public static function getHistoryFromId($model, $document_id)
    {
        return self::getHistoryFromIdAndCulture($model, $document_id, sfContext::getInstance()
                   ->getUser()
                   ->getPreferedLanguage());
    }

    public static function getHistoryFromIdAndCulture($model, $document_id, $culture)
    {
        return Doctrine_Query::create()
                             ->from('DocumentVersion d ' .
                                    'LEFT JOIN d.' . $model . 'Archive ' .
                                    'LEFT JOIN d.' . $model . 'I18nArchive ' .
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
     * @param integer
     * @param string
     * @return object
     */
    public static function getCurrentVersionInfosFromIdAndCulture($document_id, $culture)
    {
        $result = Doctrine_Query::create()
                             ->select('d.version, d.created_at')
                             ->from('DocumentVersion d')
                             ->where('d.document_id = ? AND d.culture = ?',
                                     array($document_id, $culture))
                             ->orderBy('d.version desc')
                             ->limit(1)
                             ->execute(array(), Doctrine::FETCH_ARRAY);
                             
        if (count($result))
        {
            return $result[0];
        }
        else
        {
            return null;
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
     * Get nearest docs to a point
     */
    public static function getNearest($lon, $lat, $model = 'Document', $exclude = null)
    {
        $module = c2cTools::model2module($model);
        $distance = sfConfig::get('app_autocomplete_near_max_distance');
        $limit =  sfConfig::get('app_autocomplete_suggest_max_results');

        // We have to use raw SQL because of postgis functions
        // Not that we first filter width ST_DWithin and only after that compute the dstance,
        // which is more effective
        $q = new Doctrine_RawSql();
        $q->select('{d.id}, {m.culture}, {m.name}')
          ->from('(SELECT id FROM ' . $module . ' ' .
                 'WHERE ST_DWithin(geom, ST_Transform(ST_SetSRID(ST_MakePoint(?, ?), 4326), 900913), ?) ' .
                 'AND redirects_to IS NULL ' .
                 (empty($exclude) ? '' : ' AND id NOT IN (' . implode(',', array_fill(0, sizeof($exclude), '?')) . ') ') .
                 'ORDER BY ST_Distance(geom, ST_Transform(ST_SetSRID(ST_MakePoint(?, ?), 4326), 900913)) ' .
                 'LIMIT ?) AS d ' .
                 'LEFT JOIN ' . $module . '_i18n m ON d.id = m.id')
          ->addComponent('d', $model . ' d')
          ->addComponent('m', 'd.' . $model .'I18n m');
        return $q->execute(array_merge(array($lon, $lat, $distance), (array)$exclude, array($lon, $lat, $limit)), Doctrine::FETCH_ARRAY);
    }

    /**
     * Gets a list of documents filtering on the name field.
     * Only used for autocomplete
     */
    public static function searchByName($name, $model = 'Document', $user_id = 0, $filter_personal_content = false, $exact_match = false, $coords = null)
    {
        $model_i18n = $model . 'I18n';
        $use_docid = (intval($name) > 1);
        $name = $use_docid ? intval($name) : $name;

        // basic idea is to search documents that contain the string but we may have exceptions
        // - if string appears to be a document id, directly get it (much faster)
        // - if a geoloc is given, restrict results to those within 10km (for some docs)
        // - for personal docs, we restrict to those linked to the user (except if user is a moderator)
        // - search on topoguide and forum names for users
        // - ...
        if ($use_docid)
        {
            $where_clause = 'm.redirects_to IS NULL AND mi.id = ?';
        }
        else
        {
           // FIXME change the diff between users and other modules once performance problems have been resolved
           $operator = $exact_match ? '= ?' :
               ($model == 'User' ? "LIKE make_search_name(?)||'%'" : "LIKE '%'||make_search_name(?)||'%'");
           $where_clause = 'm.redirects_to IS NULL AND mi.search_name ' . $operator;
        }

        if (isset($coords))
        {
            $where_clause = "ST_DWithin(geom, transform(setsrid(makepoint(?, ?), 4326), 900913), ?) AND " . $where_clause;
            $where_vars = array($coords['lon'], $coords['lat'], sfConfig::get('app_autocomplete_near_max_distance'));
        }
        else
        {
            $where_vars = array();
        }

        if ($model == 'Outing')
        {
            // autocomplete on outings must only return those for which the current user is linked to
            // #181: if an outing id is given, and user is moderator, we don't put this restriction
            $select = 'mi.name, m.id, m.module, m.date';
            if ($use_docid && sfContext::getInstance()->getUser()->hasCredential('moderator'))
            {
                array_push($where_vars, $name);
            }
            else
            {
                $where_clause = $where_clause . " AND m.id IN (SELECT a.linked_id FROM Association a WHERE a.type = 'uo' AND a.main_id = ?)";
                array_push($where_vars, $name, $user_id);
            }
        }
        else if (($model == 'Article') && $filter_personal_content)
        {
            // return only collaborative articles, or personal ones linked with user
            $select = 'mi.name, m.id, m.module, m.article_type';
            $where_clause = $where_clause . " AND (m.article_type = 1 OR (m.id IN (SELECT a.linked_id FROM Association a WHERE a.type = 'uc' AND a.main_id = ?)))";
            array_push($where_vars, $name, $user_id);
        }
        else if (($model == 'Image') && $filter_personal_content)
        {
            // return only collaborative images or personal ones which were uploaded by user
            $select = 'mi.name, m.id, m.module, m.image_type';
            $where_clause = $where_clause . " AND (m.image_type = 1 OR (m.image_type = 2 AND m.id IN "
                                          . "(SELECT a.id FROM Image a LEFT JOIN a.versions v ON a.id = v.document_id "
                                          . "LEFT JOIN v.history_metadata a4 ON v.history_metadata_id = a4.history_metadata_id "
                                          . "WHERE a.redirects_to IS NULL AND (v.version = 1 AND a4.user_id = ?))))";
            array_push($where_vars, $name, $user_id);
        }
        else if ($model == 'User') // search on topoguide and forum names
        {
            $select = 'mi.name, m.id, m.module, mu.username';
            $from = 'User m, m.UserI18n mi, m.private_data mu';

            array_push($where_vars, $name);
            if (!$use_docid)
            {
                $where_clause = 'm.redirects_to IS NULL AND (mi.search_name ' . $operator . ' OR mu.search_username ' . $operator . ')';
                array_push($where_vars, $name);
            }
        }
        else if ($model == 'Book') // retrieve author and publication date
        {
            $select = 'mi.name, m.id, m.module, m.author, m.publication_date';
            array_push($where_vars,$name);
        }
        else if ($model == 'Summit' || $model == 'Hut') // retrieve elevation
        {
            $select = 'mi.name, m.id, m.module, m.elevation';
            array_push($where_vars, $name);
        }
        else
        {
            $select = 'mi.name, m.id, m.module';
            array_push($where_vars, $name);
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
        if ($model == 'User')
        {
            $model_2 = 'user';
        }
        else
        {
            $model_2 = $model;
        }
        $model_i18n = $model . 'I18n';
        $selected_fields = 'DISTINCT m.id, m.module, mi.culture, mi.name';

        $q = Doctrine_Query::create()
             ->select($selected_fields)
             ->from($model_i18n . ' mi')
             ->leftJoin('mi.' . $model_2 . ' m');
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
              ->leftJoin('l.SummitI18n si')
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
     * Same as above, but return only id
     */
    public static function idSearchByName($name, $model = 'Document')
    {
        $is_connected = sfContext::getInstance()->getUser()->isConnected();
        
        $name = str_replace(array('   ', '  '), array(' ', ' '), $name);
        $name = trim($name);
        
        if ($model == 'UserPrivateData')
        {
            $q = Doctrine_Query::create()
                 ->select('pd.id')
                 ->from($model . ' pd')
                 ->where('pd.search_username LIKE \'%\'||make_search_name(?)||\'%\'', array($name));
            
            if (!$is_connected)
            {
                $q->addWhere('pd.is_profile_public = \'1\'');
            }
        }
        else
        {
            if ($model == 'User')
            {
                $model_2 = 'user';
            }
            else
            {
                $model_2 = $model;
            }
            $q = Doctrine_Query::create()
                 ->select('DISTINCT mi.id')
                 ->from($model . 'I18n' . ' mi')
                 ->leftJoin('mi.' . $model_2 . ' m')
                 ->where('mi.search_name LIKE \'%\'||make_search_name(?)||\'%\' AND m.redirects_to IS NULL', array($name));
            
            if ($model == 'User' && !$is_connected)
            {
                $q->leftJoin('m.private_data pd')
                  ->addWhere('pd.is_profile_public = \'1\'');
            }
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

    public static function convertStringToArray($string, $emptyval = null)
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
        $array = explode(',', $string);

        if ($emptyval !== null && ($key = array_search($emptyval, $array)) !== false)
        {
            unset($array[$key]);
        }

        return $array;
    }

    public static function convertStringToArrayTranslate($string, $configuration, $emptyval = null)
    {
        $f = function($value) use ($configuration)
        {
            return $configuration[$value];
        };

        return array_map($f, self::convertStringToArray($string, $emptyval));
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

    // retrieves the creator ID of the document
    public function getCreatorId()
    {
        $result = Doctrine_Query::create()
                             ->select('dv.document_id, hm.user_id')
                             ->from('DocumentVersion dv ' .
                                    'LEFT JOIN dv.history_metadata hm ')
                             ->where('dv.document_id = ? AND dv.version = ?',
                                     array($this->id, 1))
                             ->orderBy('dv.created_at ASC')
                             ->limit(1)
                             ->execute(array(), Doctrine::FETCH_ARRAY);

        if (isset($result[0]))
        {
            $creatorId = $result[0]['history_metadata']['user_id'];
        }
        else
        {
            $creatorId = 0;
        }

        return $creatorId;
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
            $linked_docs_info = Language::getTheBest($linked_docs_info, $model);
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
    
    public static function buildStringCondition(&$conditions, &$values, $field, $param, $model, $join = array(null, null))
    {
        $has_result = false;
        $has_id = false;
        $nb_result = 0;
        $join_result = null;
        
        $param = urldecode($param);
        
        
         
        if (strlen($param) > 2)
        {
            /* init solr 
             * 
             */
            $max_row = 100000;
            
           
            $options = array
            (
                'hostname' => sfConfig::get('app_solr_host'),
                'port'     => sfConfig::get('app_solr_port'),
                'path'     => sfConfig::get('app_solr_path'),
                'timeout'  => sfConfig::get('app_solr_timeout')
            );
                
            $client = new SolrClient($options);
            
    
            try {
                
                $client->ping(); 
                
             
                /* query construct */
                $query_solr = new SolrQuery();
                
                
                
                // Fuzzy search word > 3 letters
                $query_words = explode(" ", $param);
                foreach ($query_words as &$word) {
                    
                    if (strlen($word) > 3) {
                        
                        $word = $word . '~';
                    }
                }
                $query_search_fuzzy = implode('+', $query_words);
                
                $query_search = '\'('.$param.')^10 OR ('.$query_search_fuzzy.')^5\'' ;
                c2cTools::log(" solr request : " . $query_search);    

                $query_solr->setQuery($query_search);
                $query_solr->setRows($max_row);

                if (($model == 'User') || ($model == 'UserPrivateData' )) {
                    if (!sfContext::getInstance()->getUser()->isConnected()) {
                        $query_solr->addFilterQuery('user_private_public:true');
                    }
                }  
                
                $query_solr->addFilterQuery('module:'.strtolower($model).'s');
                $query_solr->addField('name')->addField('module')->addField('id_doc');
                $res = $client->query($query_solr)->getResponse();
             
                for ($i = 0; $i < $res['response']['numFound']; $i++) {
                            $ids_tmp[]['id'] = $res['response']['docs'][$i]['id_doc'];
                        }
                 }
            
            catch (Exception $e)
            {   
                c2cTools::log(" exception solr : ".$e );
                 $ids_tmp = self::idSearchByName($param, $model);
            }
            if (count($ids_tmp))
            {
                $ids = array();
                
                foreach ($ids_tmp as $id)
                {
                    $ids[] = $id['id'];
                }
                $conditions[] = $field[0] . ' IN (' . implode(',', $ids) . ')';
                
                $has_result = true;
                $nb_result = count($ids);
                $join_result = $join[0];
                if (!$join[0])
                {
                    $has_id = true;
                }
            }
        }
        else
        {
            $conditions[] = $field[1] . ' LIKE make_search_name(?)||\'%\'';
            $values[] = $param;
            
            $has_result = true;
            if ($join[1])
            {
                $join_result = $join[1];
            }
        }
        
        return array('has_result' => $has_result,
                     'has_id' => $has_id,
                     'nb_result' => $nb_result,
                     'join' => $join_result);
    }

    
    public static function buildIstringCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "($field IS NULL OR $field = '')";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL AND $field != ''";
        }
        else
        {
            $conditions[] = $field . ' ILIKE ?';
            $values[] = '%' . urldecode($param) . '%';
        }
    }
    /*
     * This function is used to search in 2 fields. If we got a :, first part is for first field,
     * second part for second field (and thus use AND)
     * Else we use OR on the two fields
     */
    public static function buildMstringCondition(&$conditions, &$values, $field, $param, $model, $join = null)
    {
        $name_list = array();
        $param_list = explode(':', $param, 2);
        $name_list[0] = urldecode(trim($param_list[0]));
        if (count($param_list) == 1)
        {
            $name_list[1] = $name_list[0];
            $condition_type = ' OR ';
            $use_or = true;
        }
        else
        {
            $name_list[1] = urldecode(trim($param_list[1]));
            $condition_type = ' AND ';
            $use_or = false;
        }
        
        $conditions_name = $joins_name = $result_list = array();
        if (empty($join))
        {
            $join = array(array(null, null), array(null, null));
        }
        
        $has_result = false;
        $has_id = false;
        $nb_result = 0;
        $join_result = null;
        $no_result = array('has_result' => false,
                           'has_id' => false,
                           'nb_result' => 0,
                           'join' => null);
        $nb_no_result = false;
        
        if (!empty($name_list[0]))
        {
            $infos = self::buildStringCondition($conditions_name, $values, $field[0], $name_list[0], $model[0], $join[0]);
            $has_result = $infos['has_result'];
            if (!$has_result && !$use_or)
            {
                $nb_no_result = true;
            }
            $result_list[] = $infos;
        }
        else
        {
            $result_list[] = $no_result;
            $use_or = true;
        }
        
        if (!empty($name_list[1]) && ($has_result || $use_or))
        {
            $infos = self::buildStringCondition($conditions_name, $values, $field[1], $name_list[1], $model[1], $join[1]);
            if (!$infos['has_result'] && !$use_or)
            {
                $nb_no_result = true;
            }
            $result_list[] = $infos;
        }
        else
        {
            $result_list[] = $no_result;
        }
        
        if ((!$result_list[0]['has_result'] && !$result_list[1]['has_result']) || $nb_no_result)
        {
            $result_list = 'no_result';
        }
        
        if (count($conditions_name) == 1)
        {
            $conditions[] = $conditions_name[0];
        }
        elseif (count($conditions_name) > 1)
        {
            $conditions[] = '(' . implode($condition_type, $conditions_name) . ')';
        }
        
        return $result_list;
    }

    public static function buildItemCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "($field IS NULL OR $field = 0)";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL AND $field != 0";
        }
        else
        {
            $conditions[] = $field . ' = ?';
            $values[] = $param;
        }
    }

    public static function buildItemNullCondition(&$conditions, &$values, $field, $param)
    {
        if (!$param || $param == '-' || $param == 'no')
        {
            $conditions[] = "($field IS NULL)";
            $result = 'null';
        }
        elseif ($param == ' ' || $param == 'yes')
        {
            $conditions[] = "$field IS NOT NULL";
            $result = 'not_null';
        }
        else
        {
            $conditions[] = $field . ' = ?';
            $values[] = $param;
            $result = $param;
        }
        
        return $result;
    }

    public static function buildMultiCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == '-')
        {
            $conditions[] = "($field IS NULL OR $field = 0)";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL AND $field != 0";
        }
        else
        {
            $conditions[] = '? = ANY(' . $field . ')';
            $values[] = $param;
        }
    }

    public static function buildCompareCondition(&$conditions, &$values, $field, $param, $is_null_only = false, $use_not_null = true)
    {
        if ($param == '-')
        {
            if ($is_null_only)
            {
                $conditions[] = "$field IS NULL";
            }
            else
            {
                $conditions[] = "($field IS NULL OR $field = 0)";
            }
        }
        elseif ($param == ' ')
        {
            if ($is_null_only)
            {
                $conditions[] = "$field IS NOT NULL";
            }
            else
            {
                $conditions[] = "$field IS NOT NULL AND $field != 0";
            }
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
            if (!$use_not_null)
            {
                $not_null = '';
            }
            elseif ($is_null_only)
            {
                $not_null = "AND $field IS NOT NULL";
            }
            else
            {
                $not_null = "AND $field IS NOT NULL AND $field != 0";
            }

            switch ($compare)
            {   
                case '>':
                    $conditions[] = "$field >= ? $not_null";
                    $values[] = $value1;
                    break;

                case '<':
                    $conditions[] = "$field <= ? $not_null";
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
        elseif (preg_match('/^([0-9!]*)(-[0-9!]*)*$/', $param, $regs))
        {
            self::buildListCondition($conditions, $values, $field, $param, $use_not_null);
        }
        else
        {
            return;
        }
    }

    
    public static function buildRelativeCondition(&$conditions, &$values, $field, $param)
    {
        if (!is_array($field) || count($field) != 2)
        {
            return;
        }
        
        list($field_a, $field_b) = $field;
        if (is_array($field_a))
        {
            $field_a_1 = $field_a[0] . '.' . $field_a[2];
            $field_a_2 = $field_a[1] . '.' . $field_a[2];
        }
        else
        {
            $field_a_1 = $field_a_2 = $field_a;
        }
        if (is_array($field_b))
        {
            $field_b_1 = $field_b[0] . '.' . $field_b[2];
            $field_b_2 = $field_b[1] . '.' . $field_b[2];
        }
        else
        {
            $field_b_1 = $field_b_2 = $field_b;
        }
        
        if ($param == '-')
        {
            $conditions[] = "$field_a_1 = $field_b_1";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field_a_1 != $field_b_1";
        }
        elseif(preg_match('/^([><]?)(-?)([0-9]*)(~?)([0-9]*)$/', $param, $regs))
        {
            if (empty($regs[3]))
            {
                return;
            }
            
            if (!empty($regs[1]))
            {
                if (!empty($regs[2]))
                {
                    $field_tmp = $field_a_1;
                    $field_a_1 = $field_b_1;
                    $field_b_1 = $field_tmp;
                    $field_tmp = $field_a_2;
                    $field_a_2 = $field_b_2;
                    $field_b_2 = $field_tmp;
                    if ($regs[1] == '>')
                    {
                        $regs[1] = '<';
                    }
                    else
                    {
                        $regs[1] = '>';
                    }
                }
                $not_null = "$field_a_1 >= $field_b_1 AND ";
            }
            else
            {
                $not_null = '';
            }
            
            $field_compare = $not_null . "($field_a_2 - $field_b_2)";
            $param_compare = $regs[1] . $regs[3] . $regs[4] . $regs[5];
            
            self::buildCompareCondition($conditions, $values, $field_compare, $param_compare, false, false);
        }
        else
        {
            return;
        }
    }

    public static function buildListCondition(&$conditions, &$values, $field, $param, $use_not_null = true)
    {
        $nb_id = 0;
        
        if ($param == '-')
        {
            $conditions[] = "($field IS NULL OR $field = 0)";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field IS NOT NULL AND $field != 0";
        }
        elseif (preg_match('/^([0-9a-z!]*)(-[0-9a-z!]*)*$/', $param, $regs))
        {
            $items = explode('-', $param);
            $condition_array = array();
            $not_condition_array = array();
            $not_values = array();
            $is_null = '';
            $condition = array();
            $conditions_groups = array();
            foreach ($items as $item)
            {
                if ($item == '')
                {
                    continue;
                }
                $not_items = explode('!', $item);
                $item = array_shift($not_items);
                if ($item != '')
                {
                    if (strval($item) != '0')
                    {
                        $condition_array[] = '?';
                        $values[] = $item;
                    }
                    else
                    {
                        $is_null = "$field IS NULL OR $field = 0";
                    }
                }
                if (count($not_items))
                {
                    foreach ($not_items as $not_item)
                    {
                        $not_condition_array[] = '?';
                        $not_values[] = $not_item;
                    }
                }
            }
            if ($nb_id = count($condition_array))
            {
                if ($nb_id == 1)
                {
                    $condition[] = $field . ' = ?';
                    $simple_value = true;
                }
                elseif ($nb_id > 1)
                {
                    $condition[] = $field . ' IN ( ' . implode(', ', $condition_array) . ' )';
                }
            }
            if (!empty($is_null))
            {
                $condition[] = $is_null;
            }
            if (count($condition))
            {
                $conditions_groups[] = '((' . implode(') OR (', $condition) . '))';
            }
            if ($nb_not_conditions = count($not_condition_array))
            {
                if ($nb_not_conditions == 1)
                {
                    $conditions_groups[] = $field . ' != ?';
                }
                elseif ($nb_not_conditions > 1)
                {
                    $conditions_groups[] = $field . ' NOT IN ( ' . implode(', ', $not_condition_array) . ' )';
                }
                foreach ($not_values as $value)
                {
                    $values[] = $value;
                }
            }
            $conditions[] = implode(' AND ', $conditions_groups);
        }
        elseif (preg_match('/^(>|<)?([0-9]*)(~)?([0-9]*)$/', $param, $regs))
        {
            self::buildCompareCondition($conditions, $values, $field, $param, false, $use_not_null);
        }
        
        return $nb_id;
    }

    public static function buildMultiIdCondition(&$conditions, &$values, $field, $param)
    {
        $group_id = 0;
        $nb_id = 0;
        
        if (($param == '-') || ($param == ' '))
        {
            $field_1 = $field[0] . '1.' . $field[1];
            if ($param == '-')
            {
                $conditions[] = "($field_1 IS NULL OR $field_1 = 0)";
            }
            elseif ($param == ' ')
            {
                $conditions[] = "$field_1 IS NOT NULL AND $field_1 != 0";
            }
            
            $group_id = 1;
        }
        elseif (preg_match('/^(>|<)?([0-9]*)(~)?([0-9]*)$/', $param, $regs))
        {
            $field_1 = $field[0] . '1.' . $field[1];
            self::buildCompareCondition($conditions, $values, $field_1, $param, false, false);
            $group_id = 1;
        }
        else
        {
            $item_groups = explode(' ', $param);
            $conditions_groups = array();
            foreach ($item_groups as $group)
            {
                $group_id += 1;
                $field_n = $field[0] . $group_id . '.' . $field[1];
                $items = explode('-', $group);
                $condition_array = array();
                $not_condition_array = array();
                $not_values = array();
                $condition = array();
                $is_null = '';
                foreach ($items as $item)
                {
                    $not_items = explode('!', $item);
                    $item = array_shift($not_items);
                    if ($item != '')
                    {
                        if (strval($item) != '0')
                        {
                            $condition_array[] = '?';
                            $values[] = $item;
                        }
                        else
                        {
                            $is_null = "$field_n IS NULL OR $field_n = 0";
                        }
                    }
                    if (count($not_items))
                    {
                        foreach ($not_items as $not_item)
                        {
                            $not_condition_array[] = '?';
                            $not_values[] = $not_item;
                        }
                    }
                }
                if ($nb_id = count($condition_array))
                {
                    if ($nb_id == 1)
                    {
                        $condition[] = $field_n . ' = ?';
                    }
                    elseif ($nb_id > 1)
                    {
                        $condition[] = $field_n . ' IN ( ' . implode(', ', $condition_array) . ' )';
                    }
                }
                if (!empty($is_null))
                {
                    $condition[] = $is_null;
                }
                if (count($condition))
                {
                    $conditions_groups[] = '((' . implode(') OR (', $condition) . '))';
                }
                if ($nb_not_conditions = count($not_condition_array))
                {
                    if ($nb_not_conditions == 1)
                    {
                        $conditions_groups[] = $field_n . ' != ?';
                    }
                    elseif ($nb_not_conditions > 1)
                    {
                        $conditions_groups[] = $field_n . ' NOT IN ( ' . implode(', ', $not_condition_array) . ' )';
                    }
                    foreach ($not_values as $value)
                    {
                        $values[] = $value;
                    }
                }
            }
            
            $conditions[] = implode(' AND ', $conditions_groups);
            
            if ($group_id > 1)
            {
                $nb_id = 0;
            }
        }
        return array('nb_group' => $group_id,
                     'nb_id' => $nb_id);
    }

    public static function buildLinkedlistCondition(&$conditions, &$values, $field, $param)
    {
        $field_0 = $field[0] . '.' . $field[1];
        $field_1 = $field[0] . '1.' . $field[1];
        $field_list = array(0 => $field_0, 1 => $field_1);
        
        if ($param == '-')
        {
            $conditions[] = "($field_1 IS NULL OR $field_1 = 0)";
        }
        elseif ($param == ' ')
        {
            $conditions[] = "$field_1 IS NOT NULL AND $field_1 != 0";
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
            
            $conditions[] = '((' . implode(') OR (', $conditions_groups) . '))';
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

        // unfortunately, we use postgresql arrays with integers only EXCEPT for books langs
        // so we need special handling ine the two blocks below. Note that tehre is no equivalent to 0 value for books
        // TODO update code so that languages in books are coded with integers...
        if ($param == '-')
        {
            if ($field_1 === 'm.langs')
            {
                $conditions[] = "($field_1 IS NULL)";
            }
            else
            {
                $conditions[] = "($field_1 IS NULL OR 0 = ANY ($field_2))";
            }
        }
        elseif ($param == ' ')
        {
            if ($field_1 === 'm.langs')
            {
                $conditions[] = "($field_1 IS NOT NULL)";
            }
            else
            {
                $conditions[] = "$field_1 IS NOT NULL AND NOT (0 = ANY ($field_2))";
            }
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
                $cond = "? = ANY ($field_2)";
                foreach ($items as $item)
                {
                    $not_items = explode('!', $item);
                    $item = array_shift($not_items);
                    if ($item != '')
                    {
                        if (strval($item) != '0')
                        {
                            $condition_array[] = $cond;
                            $values[] = $item;
                        }
                        elseif (!$is_null)
                        {
                            $conditions_groups[] = "$field_1 IS NULL OR 0 = ANY ($field_2)";
                            $is_null = true;
                        }
                    }
                    if (count($not_items))
                    {
                        foreach ($not_items as $not_item)
                        {
                            $condition_array[] = "NOT $cond";
                            $values[] = $not_item;
                        }
                    }
                }
                if (count($condition_array))
                {
                    $conditions_group = implode(' AND ', $condition_array);
                    if (count($condition_array) > 1 && count($item_groups) > 1)
                    {
                        $conditions_group = '(' . $conditions_group . ')';
                    }
                    $conditions_groups[] = $conditions_group;
                }
            }
            if (count($conditions_groups) == 1)
            {
                $conditions_groups = $conditions_groups[0];
            }
            else
            {
                $conditions_groups = '(' . implode(' OR ', $conditions_groups) . ')';
            }
            $conditions[] = $conditions_groups;
        }
    }

    public static function buildBoolCondition(&$conditions, &$values, $field, $param)
    {
        if ($param == 'yes' || $param == '1')
        {
            $conditions[] = $field;
        }
        else
        {
            $conditions[] = $field . ' IS NOT TRUE';
        } 
    }

    public static function buildConfigCondition(&$joins, $join, $param)
    {
        if ($param == 'yes' || $param == '1' || $param = ' ')
        {
            $joins[$join] = true;
        }
        elseif ($param == 'no' || $param == '0' || $param = '-')
        {
            $joins[$join] = false;
        }
        elseif (!empty($param))
        {
            $joins[$join] = $param;
        }
    }

    public static function buildJoinCondition(&$joins, &$values, $join0 = '', $param, $join1 = '')
    {
        if (!empty($param))
        {
            if (!empty($join0))
            {
                $join_key = $param . '_' . $join0;
            }
            else
            {
                $join_key = 'post_' . $param;
            }
            $joins[$join_key] = true;
            $joins['join_' . $param] = true;
            if (!empty($join1))
            {
                $joins['join_' . $join1] = true;
            }
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
            $conditions[] = "$field IS NOT NULL";
        }
        else
        {
            $conditions[] = "$field IS NULL";
        } 
    }

    public static function buildFacingCondition(&$conditions, &$values, $field, $param)
    {
        $facings = explode('~', $param);
        if (count($facings) == 1)
        {
            if ($param == '-')
            {
                $conditions[] = "($field IS NULL OR $field = 0)";
            }
            elseif ($param == ' ')
            {
                $conditions[] = "$field IS NOT NULL AND $field != 0";
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
                $conditions[] = "($field <= ? OR $field >= ?)";
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
        elseif (preg_match('/^(>|<)?([0-9]*-?)(~)?([0-9]*)$/', $param, $regs))
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
                        self::buildCompareCondition($conditions, $values, $field, $param, true);
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
                    self::buildCompareCondition($conditions, $values, $field, $newparam, true);
                    break;
                case 4: // YYYY or MMDD
                    // TODO check input values
                    if ((int)$value1 > 1231) // YYYY
                    {
                        switch ($compare)
                        {
                            case '>':
                                $newparam = $compare . $value1 . '0101';
                                break;
                            case '<':
                                // we need to provide a valid date
                                $newparam = $compare . $value1 . '1231';
                                break;
                            case '=':
                                // we need to provide a valid date
                                $newparam = $value1 . '0101~' . $value1 . '1231';
                                break;
                            case '~':
                                // we make sure that date1 < date2
                                $newparam = min($value1, $value2) . '0101' . $compare . max($value1, $value2) . '1231';
                                break;
                        }
                        self::buildCompareCondition($conditions, $values, $field, $newparam, true);
                    }
                    else // MMDD
                    {
                        switch ($compare)
                        {
                            case '>':
                                $conditions[] = "date_part('month', $field) > ? OR (date_part('month', $field) = ? AND date_part('day', $field) >= ?)";
                                $month = substr($value1, 0, 2);
                                $values[] = $month;
                                $values[] = $month;
                                $values[] = substr($value1, 2, 2);
                                break;
                            case '<':
                                $conditions[] = "date_part('month', $field) < ? OR (date_part('month', $field) = ? AND date_part('day', $field) <= ?)";
                                $month = substr($value1, 0, 2);
                                $values[] = $month;
                                $values[] = $month;
                                $values[] = substr($value1, 2, 2);
                                break;
                            case '=':
                                $conditions[] = "date_part('month', $field) = ? AND date_part('day', $field) = ?";
                                $values[] = substr($value1, 0, 2);
                                $values[] = substr($value1, 2, 2);
                                break;
                            case '~': // youpi
                                if ($value1 <= $value2)
                                {
                                    $conditions[] = "(date_part('month', $field) > ? OR (date_part('month', $field) = ? AND date_part('day', $field) >= ?)) AND ".
                                                    "date_part('month', $field) < ? OR (date_part('month', $field) = ? AND date_part('day', $field) <= ?)";
                                }
                                else
                                {
                                    $conditions[] = "(date_part('month', $field) > ? OR (date_part('month', $field) = ? AND date_part('day', $field) >= ?)) OR ".
                                                    "date_part('month', $field) < ? OR (date_part('month', $field) = ? AND date_part('day', $field) <= ?)";
                                }
                                $month = substr($value1, 0, 2);
                                $day = substr($value1, 2, 2);
                                $values[] = $month;$values[] = $month;$values[] = $day;
                                $month = substr($value2, 0, 2);
                                $day = substr($value2, 2, 2);
                                $values[] = $month;$values[] = $month;$values[] = $day;
                                break;
                        }
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
                                $conditions[] = "date_part('month', $field) >= ?";
                                $values[] = $value1;
                                break;
                            case '<':
                                $conditions[] = "date_part('month', $field) <= ?";
                                $values[] = $value1;
                                break;
                            case '=':
                                $conditions[] = "date_part('month', $field) = ?";
                                $values[] = $value1;
                                break;
                            case '~':
                                if ($value1 <= $value2) // like between july and august
                                {
                                    $conditions[] = "date_part('month', $field) BETWEEN ? AND ?";
                                }
                                else // like between november and march
                                {
                                    $conditions[] = "(date_part('month', $field) >= ? OR date_part('month', $field) <= ?)";
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
        $where = gisQuery::getQueryByBbox($param, $field);
        $conditions[] = $where['where_string'];
    }

    public static function buildAroundCondition(&$conditions, &$values, $field, $param)
    {
        // new input format is /$lon,$lat~$range with . as decimal mark
        // old input format is /$lon-$lat,$range with , as decimal mark
        if (preg_match('/^(-?\d*\.?\d+),(-?\d*\.?\d+),(\d+)/', $param, $matches) ||
            preg_match('/^(-?\d*\.?\d+)-(-?\d*\.?\d+)~(\d+)/', strtr($param, ',', '.'), $matches)) {
            // data could be with lon,lat (EPSG:4326) (with such values, it is very unlikely to be 900913 coordinates
            if ((-180 < (float) $matches[1]) && ((float) $matches[1] < 180) &&
                (-90 < (float) $matches[2]) && ((float) $matches[2] < 90))
            {
                $srid = 4326;
            }
            else // or assumed to be EPSG:900913
            {
                $srid = 900913;
            }

            self::buildXYCondition($conditions, $values, (float) $matches[1], (float) $matches[2],
                                   (int) $matches[3], $field, $srid);
        }
    }
    
    /* x y must be with SRID 4326 or 900913 */
    public static function buildXYCondition(&$conditions, &$values, $x, $y, $tolerance, $field = 'geom', $srid = 900913)
    {
        $conditions[] = ($srid == 900913) ? 'ST_DWithin(ST_SetSRID(MAKEPOINT(?,?), 900913), ' . $field . ', ?)'
                                           : 'ST_DWithin(ST_Transform(ST_SetSRID(MAKEPOINT(?,?), 4326), 900913), ' . $field . ', ?)';
        array_push($values, $x, $y, round($tolerance));
    }

    public static function buildOrderCondition(&$joins, &$orderby_list, $params, $join_ids)
    {
        if (empty($orderby_list))
        {
            return 0;
        }
        
        if (array_intersect($params, $orderby_list))
        {
            if (!is_array($join_ids))
            {
                $join_ids = array($join_ids);
            }
            foreach ($join_ids as $join_id)
            {
                $joins[$join_id] = true;
            }
            
            return true;
        }
        else
        {
            return false;
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