<?php
/*
 * $Id: MapArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class MapArchive extends BaseMapArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('MapArchive')->find($id);
    }
}
