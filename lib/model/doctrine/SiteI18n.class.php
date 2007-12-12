<?php
/**
 * $Id: SiteI18n.class.php 1971 2007-10-03 17:43:34Z alex $
 */
class SiteI18n extends BaseSiteI18n
{
    public static function filterSetRemarks($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
    
    public static function filterSetPedestrian_access($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
    
    public static function filterSetWay_back($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
