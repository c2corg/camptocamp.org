<?php
/*
 * $Id: AreaArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class AreaArchive extends BaseAreaArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('AreaArchive')->find($id);
    }
}
