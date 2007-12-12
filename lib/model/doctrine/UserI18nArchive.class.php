<?php
/*
 * $Id: UserI18nArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class UserI18nArchive extends BaseUserI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('UserI18nArchive')->find($id);
    }
}
