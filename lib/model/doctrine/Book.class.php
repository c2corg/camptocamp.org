<?php
/**
 * $Id: Book.class.php 2261 2007-11-03 15:05:40Z alex $
 */
class Book extends BaseBook
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetLangs($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetLangs($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetBook_types($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetBook_types($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetAuthor($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetEditor($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetUrl($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetIsbn($value)
    {
        return self::returnNullIfEmpty($value);
    }
}
