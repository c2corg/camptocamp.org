<?php

class PortalI18nArchive extends BasePortalI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('PortalI18nArchive')->find($id);
    }
}
