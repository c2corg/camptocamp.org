<?php
/**
 * $Id: ArticleArchive.class.php 1260 2007-08-13 19:29:02Z alex $
 */
class ArticleArchive extends BaseArticleArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ArticleArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }

    public static function filterGetCategories($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }
}
