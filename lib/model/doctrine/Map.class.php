<?php
/**
 * Model for maps
 * $Id: Map.class.php 1971 2007-10-03 17:43:34Z alex $
 */

class Map extends BaseMap
{
    public static function filterSetEditor($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetScale($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function filterSetCode($value)
    {
        return self::returnNullIfEmpty($value);
    }
}
