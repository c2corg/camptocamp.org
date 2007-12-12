<?php
/**
 * $Id: ParkingArchive.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */
class ParkingArchive extends BaseParkingArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ParkingArchive')->find($id);
    }
}
