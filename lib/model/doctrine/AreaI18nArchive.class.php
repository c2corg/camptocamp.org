<?php
/*
 * $Id: AreaI18nArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class AreaI18nArchive extends BaseAreaI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('AreaI18nArchive')->find($id);
    }
}
