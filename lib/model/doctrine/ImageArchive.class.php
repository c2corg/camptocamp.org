<?php
/*
 * $Id: ImageArchive.class.php 2173 2007-10-24 20:11:15Z alex $
 */
class ImageArchive extends BaseImageArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ImageArchive')->find($id);
    }

    public static function filterGetCategories($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }

    public static function filterGetActivities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }
}
