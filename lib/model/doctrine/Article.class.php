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
    public static function listLatest($max_items, $langs, $activities)
    {
        $sql = 'SELECT a.id, n.culture, n.name, n.abstract, n.search_name, date_trunc(\'day\', d.created_at) as date ' .
               'FROM articles a LEFT JOIN articles_i18n n ON a.id = n.id ' .
               'LEFT JOIN app_documents_versions d ON a.id = d.document_id AND d.version = 1 AND n.culture = d.culture ';

        $criteria = array();
        $where = '';

        if (!empty($activities))
        {   
            $where .= 'WHERE (' . self::getActivitiesQueryString($activities) . ') ';
            $criteria = $activities;
        }   

        if (!empty($langs))
        {   
            $where .= $where ? 'AND ' : 'WHERE ';
            $where .= self::getLanguagesQueryString($langs, 'n') . ' ';
            $criteria = array_merge($criteria, $langs);
        }

        $sql .= "$where ORDER BY d.created_at DESC, a.id DESC LIMIT $max_items";
        return sfDoctrine::connection()->standaloneQuery($sql, $criteria)->fetchAll();
    }

    
    public static function browse($sort, $criteria, $format = null)
    {
        $pager = self::createPager('Article', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.

            $conditions = $criteria[0];

            $conditions = self::joinOnMulti($q, $conditions, 'join_user_id', 'm.associations u', 4);
            
            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
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
