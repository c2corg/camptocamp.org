<?php
/**
 * $Id: Article.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Article extends BaseArticle
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetCategories($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetCategories($value)
    {   
        return self::convertStringToArray($value);
    }

    /**
     * Retrieves a list of articles ordered by descending id (~date of creation).
     */
    public static function listLatest($max_items, $langs, $activities, $params = array())
    {
        $q = Doctrine_Query::create();
        $q->select('m.id, n.culture, n.name, n.abstract')
          ->from('Article m')
          ->leftJoin('m.ArticleI18n n')
          ->leftJoin('m.versions d ON m.id = d.document_id AND d.version = 1 AND n.culture = d.culture')
          ->addWhere('m.redirects_to IS NULL')
          ->orderBy('d.created_at DESC, m.id DESC')
          ->limit($max_items);

        self::filterOnActivities($q, $activities, 'm', 'a');
        self::filterOnLanguages($q, $langs, 'n');
        
        if (!empty($params))
        {
            $criteria = self::buildListCriteria($params);
            if (!empty($criteria))
            {
                self::buildPagerConditions($q, $criteria[0], $criteria[1]);
            }
        }

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildConditionItem($conditions, $values, 'Config', '', 'all', 'all', false, $params_list);
        if (isset($conditions['all']) && $conditions['all'])
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list);
        
        // article criteria
        self::buildConditionItem($conditions, $values, 'String', 'mi.search_name', array('cnam', 'name'), null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Multi', 'a.categories', 'ccat', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Item', 'm.article_type', 'ctyp', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Array', array('m', 'a', 'activities'), 'act', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'List', 'm.id', 'id', null, false, $params_list);

        // user criteria
        self::buildConditionItem($conditions, $values, 'Multilist', array('u', 'main_id'), 'user', 'join_user_id', false, $params_list);
        self::buildConditionItem($conditions, $values, 'Multilist', array('u', 'main_id'), 'users', 'join_user_id', false, $params_list);

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }
    
    public static function browse($sort, $criteria, $format = null)
    {
        $pager = self::createPager('Article', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        $conditions = array();
        $all = false;
        if (!empty($criteria))
        {
            $conditions = $criteria[0];
            if (isset($conditions['all']))
            {
                $all = $conditions['all'];
                unset($conditions['all']);
            }
        }
        
        if (!$all && !empty($conditions))
        {
            self::buildPagerConditions($q, $conditions, $criteria[1]);
        }
        elseif (!$all && c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            self::filterOnActivities($q);
            self::filterOnLanguages($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnLinkedDocMultiRegions($q, $conditions, array('hc', 'pc', 'oc', 'rc', 'tc', 'sc', 'fc'));

        $conditions = self::joinOnMulti($q, $conditions, 'join_user_id', 'm.associations u', 4);
        
        $q->addWhere(implode(' AND ', $conditions), $criteria);
    }

    protected static function buildFieldsList()
    {   
        return array_merge(parent::buildFieldsList(), 
                           array('m.categories', 'm.activities', 'm.article_type'));
    } 

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnI18n($q, $model);
        self::filterOnActivities($q);
        self::filterOnLanguages($q);
    }
}
