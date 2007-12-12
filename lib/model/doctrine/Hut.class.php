<?php
/**
 * $Id: Hut.class.php 2168 2007-10-24 16:03:29Z alex $
 */
class Hut extends BaseHut
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetStaffed_period($value)
    {   
        return self::returnNullIfEmpty($value);
    }   

    public static function filterSetStaffed_capacity($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetUnstaffed_capacity($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetShelter_type($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetPhone($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetUrl($value)
    {
        return self::returnNullIfEmpty($value);
    }
}
