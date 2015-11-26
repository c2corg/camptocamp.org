<?php

class XreportI18nArchive extends BaseXreportI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('XreportI18nArchive')->find($id);
    }
}
