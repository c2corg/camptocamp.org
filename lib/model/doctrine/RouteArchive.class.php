<?php
/*
 * $Id: RouteArchive.class.php 1671 2007-09-18 10:18:46Z alex $
 */
class RouteArchive extends BaseRouteArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('RouteArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }  

    public static function filterGetConfiguration($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }  

    public static function filterGetSub_activities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }  

    public static function filterGetRoute_length($value)
    {   
        return round($value / 1000, 1); 
    }
}
