<?php

class ProductI18n extends BaseProductI18n
{
    public static function filterSetHours($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
    
    public static function filterSetAccess($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
