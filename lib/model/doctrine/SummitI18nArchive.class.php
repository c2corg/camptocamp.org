<?php
/*
 * $Id: SummitI18nArchive.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class SummitI18nArchive extends BaseSummitI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('SummitI18nArchive')->find($id);
    }
}
