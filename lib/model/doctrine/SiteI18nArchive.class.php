<?php
/**
 * $Id: SiteI18nArchive.class.php 1469 2007-08-28 10:07:55Z alex $
 */
class SiteI18nArchive extends BaseSiteI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('SiteI18nArchive')->find($id);
    }
}
