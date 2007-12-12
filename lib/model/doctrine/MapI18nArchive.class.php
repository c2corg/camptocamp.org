<?php
/*
 * $Id: MapI18nArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class MapI18nArchive extends BaseMapI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('MapI18nArchive')->find($id);
    }
}
