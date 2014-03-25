<?php
$item_i18n = $item->getRaw('MapI18n');
$item_i18n = $item_i18n[0];

$ms = sfConfig::get('mod_maps_scales_list');
$me = sfConfig::get('mod_maps_editors_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => geojson_geometry($item),
    'id' => $item['id'],
    'properties' => array(
        'module' => 'maps',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'maps'),
        'code' => $item['code'],
        'scale' =>  $ms[$item['scale']],
        'editor' => $me[$item['editor']],
        'nbLinkedImages' =>  isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
