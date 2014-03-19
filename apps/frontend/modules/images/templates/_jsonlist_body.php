<?php
use_helper('Field');
$item_i18n = $item->getRaw('ImageI18n');
$item_i18n = $item_i18n[0];

$it = sfConfig::get('mod_images_type_full_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'images',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'images'),
        'type' => $it[$item['image_type']],
        'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
    )
));
