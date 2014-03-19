<?php
use_helper('Link');

// retrieve the geometry as geojson
// For point geometry, we don't store elevation in db, but let's 
// add it if it's available
function geojson_geometry($item)
{
    $geometry = json_decode(gisQuery::EWKT2GeoJSON(doctrine_value($item->get('geom_wkt'))));

    if ($geometry->type === 'Point' && doctrine_value($item['elevation']))
    {
        $geometry->coordinates[] = $item['elevation'];
    }

    return $geometry;
}

function jsonlist_url($item, $module, $prefix = null)
{
    return absolute_link(url_for("@document_by_id_lang_slug?module=$module&id=" . $item['id'] .
        '&lang=' . $item['culture'] . '&slug=' . make_slug(isset($prefix) ? $prefix . '-' . $item['name'] : $item['name'])));
}
