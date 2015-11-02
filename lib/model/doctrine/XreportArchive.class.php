<?php

class XreportArchive extends BaseXreportArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('XreportArchive')->find($id);
    }

    public static function filterGetActivities($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }

    public static function filterGetEvent_type($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }
}
