<?php
/**
 * $Id$
 */
class HutI18n extends BaseHutI18n
{
    public static function filterSetStaffed_period($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetPedestrian_Access($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
