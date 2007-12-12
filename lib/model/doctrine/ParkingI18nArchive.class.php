<?php
/**
 * $Id: ParkingI18nArchive.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class ParkingI18nArchive extends BaseParkingI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ParkingI18nArchive')->find($id);
    }
}
