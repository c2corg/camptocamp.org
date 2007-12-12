<?php
/**
 * $Id: BookI18nArchive.class.php 1317 2007-08-16 22:20:40Z alex $
 */
class BookI18nArchive extends BaseBookI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('BookI18nArchive')->find($id);
    }
}
