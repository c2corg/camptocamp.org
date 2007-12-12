<?php
/*
 * $Id: HistoryMetadata.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class HistoryMetadata extends BaseHistoryMetadata
{
    public static function find($id)
    {   
        return sfDoctrine::getTable('HistoryMetadata')->find($id);
    }
}
