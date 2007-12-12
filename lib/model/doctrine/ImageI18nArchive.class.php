<?php
/*
 * $Id: ImageI18nArchive.class.php 1078 2007-07-27 12:19:46Z alex $
 */
class ImageI18nArchive extends BaseImageI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ImageI18nArchive')->find($id);
    }
}
