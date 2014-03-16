<?php
$item_i18n = $item->getRaw('AreaI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))), // NOTE geom_wkt is ST_simplified, see data/sql/tables
    'properties' => array(
        'module' => 'areas',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'areas'),
        'type' =>  $item['area_type'],
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
