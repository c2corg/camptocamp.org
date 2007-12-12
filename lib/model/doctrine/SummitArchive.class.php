<?php
/*
 * $Id: SummitArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class SummitArchive extends BaseSummitArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('SummitArchive')->find($id);
    }
}
