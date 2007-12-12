<?php
/**
 * $Id: HutArchive.class.php 2260 2007-11-03 15:02:03Z alex $
 */
class HutArchive extends BaseHutArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('HutArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }   
}
