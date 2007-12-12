<?php
/**
 * $Id: ArticleI18nArchive.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class ArticleI18nArchive extends BaseArticleI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ArticleI18nArchive')->find($id);
    }
}
