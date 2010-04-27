<?php

class PortalI18n extends BasePortalI18n
{
    public static function filterSetAbstract($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
