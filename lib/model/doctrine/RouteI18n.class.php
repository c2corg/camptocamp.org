<?php
/*
 * $Id: RouteI18n.class.php 1971 2007-10-03 17:43:34Z alex $
 */
class RouteI18n extends BaseRouteI18n
{
    public static function filterSetV4_id($value)
    {   
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetV4_app($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetRemarks($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetGear($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetExternal_resources($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
