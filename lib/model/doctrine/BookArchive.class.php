<?php
/**
 * $Id: BookArchive.class.php 2261 2007-11-03 15:05:40Z alex $
 */
class BookArchive extends BaseBookArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('BookArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }

    public static function filterGetLangs($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }

    public static function filterGetBook_types($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }
}
