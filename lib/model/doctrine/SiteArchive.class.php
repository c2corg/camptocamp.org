<?php
/**
 * $Id: SiteArchive.class.php 1936 2007-09-30 16:28:52Z alex $
 */
class SiteArchive extends BaseSiteArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('SiteArchive')->find($id);
    }

    public static function filterGetClimbing_styles($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }   

    public static function filterGetRock_types($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }   

    public static function filterGetSite_types($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }   

    public static function filterGetFacings($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }   

    public static function filterGetBest_periods($value)
    {   
        return BaseDocument::convertStringToArray($value);
    } 
}
