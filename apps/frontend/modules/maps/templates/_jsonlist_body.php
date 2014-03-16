<?php
$item_i18n = $item->getRaw('MapI18n');
$item_i18n = $item_i18n[0];

$a = sfConfig::get('mod_maps_editors_list');

echo json_encode(array(
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'maps',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'maps'),
        'code' => $item['code'],
        'scale' =>  $item['scale'],
        'editor' => $a[$item['editor']],
        'nbLinkedImages' =>  isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
