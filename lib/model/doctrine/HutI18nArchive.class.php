<?php
/**
 * $Id$
 */
class HutI18nArchive extends BaseHutI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('HutI18nArchive')->find($id);
    }
}
