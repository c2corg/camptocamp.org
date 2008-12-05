<?php
/**
 * $Id: UserArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */

class UserArchive extends BaseUserArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('UserArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {
        return BaseDocument::convertStringToArray($value);
    }
}
