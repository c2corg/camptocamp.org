<?php

class PortalArchive extends BasePortalArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('PortalArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }   
}
