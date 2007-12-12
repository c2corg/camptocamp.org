<?php
/**
 * $Id: Parking.class.php 1971 2007-10-03 17:43:34Z alex $
 */
class Parking extends BaseParking
{
    public static function filterSetElevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetLowest_elevation($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetPublic_transportation_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetSnow_clearance_rating($value)
    {
        return self::returnPosIntOrNull($value);
    }
}
