<?php
/**
 * $Id: ParkingI18n.class.php 1971 2007-10-03 17:43:34Z alex $
 */
class ParkingI18n extends BaseParkingI18n
{
    public static function filterSetPublic_transportation_description($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetSnow_clearance_comment($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetAccommodation($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
