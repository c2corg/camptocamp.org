<?php
/**
 * $Id: Article.class.php 2298 2007-11-05 22:05:51Z alex $
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
        $sql = 'SELECT a.id, n.culture, n.name, extract(day from d.created_at) as day, extract(month from d.created_at) as month ' .
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
}
