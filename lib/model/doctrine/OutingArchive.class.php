<?php
/*
 * $Id: OutingArchive.class.php 1582 2007-09-06 09:43:20Z alex $
 */
class OutingArchive extends BaseOutingArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('OutingArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {
        return BaseDocument::convertStringToArray($value);
    }

    public static function filterGetOuting_length($value)
    {
        return round($value / 1000, 1);
    }

    public static function filterGetAvalanche_date($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }
}
