<?php

class ProductArchive extends BaseProductArchive
{
    public static function find($id)
    {
        return sfDoctrine::getTable('ProductArchive')->find($id);
    }
}
