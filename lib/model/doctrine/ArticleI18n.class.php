<?php
/**
 * $Id: ArticleI18n.class.php 1971 2007-10-03 17:43:34Z alex $
 */
class ArticleI18n extends BaseArticleI18n
{
    public static function filterSetAbstract($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
