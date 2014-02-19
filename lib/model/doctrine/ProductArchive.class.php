<?php

class ProductArchive extends BaseProductArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ProductArchive')->find($id);
    }

    public static function filterGetProduct_type($value)
    {   
        return BaseDocument::convertStringToArray($value);
    }
}
