<?php

class ProductI18nArchive extends BaseProductI18nArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ProductI18nArchive')->find($id);
    }
}
