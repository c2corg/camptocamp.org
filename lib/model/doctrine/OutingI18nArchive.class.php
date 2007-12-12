<?php
/*
 * $Id: OutingI18nArchive.class.php 1300 2007-08-15 20:23:09Z alex $
 */
class OutingI18nArchive extends BaseOutingI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('OutingI18nArchive')->find($id);
    }

    public static function filterGetConditions_levels($value)
    {   
        return unserialize($value);
    }
}
