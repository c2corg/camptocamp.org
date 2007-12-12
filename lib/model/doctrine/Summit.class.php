<?php
/**
 * Model for summits
 * $Id: Summit.class.php 1971 2007-10-03 17:43:34Z alex $
 */

class Summit extends BaseSummit
{
    public static function filterSetElevation($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetSummit_type($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetMaps_info($value)
    {
        return self::returnNullIfEmpty($value);
    }
}
