<?php

class XreportArchive extends BaseXreportArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('XreportArchive')->find($id);
    }
}
