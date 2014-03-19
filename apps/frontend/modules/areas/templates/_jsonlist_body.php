<?php
$item_i18n = $item->getRaw('AreaI18n');
$item_i18n = $item_i18n[0];

$at = sfCOnfig::get('app_areas_area_types');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => geojson_geometry($item), // NOTE geom_wkt is ST_simplified for areas, see data/sql/tables
    'properties' => array(
        'module' => 'areas',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'areas'),
        'type' =>  $at[$item['area_type']],
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
