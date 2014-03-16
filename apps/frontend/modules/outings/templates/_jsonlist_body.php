<?php
use_helper('Field');
$item_i18n = $item->getRaw('OutingI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'outings',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'outings'),
        'activities' => BaseDocument::convertStringToArray($item['activities']),
        'creator' => $item['creator'],
        'maxElevation' => check_not_empty($item->getRaw('max_elevation')) ? $item['max_elevation'] : null,
        'heightDiffUp' => check_not_empty($item->getRaw('height_diff_up')) ? $item['height_diff_up'] : null,
        'routes_rating' => isset($item['linked_routes']) ? field_route_ratings_data($item, false, false, false, 'json') : null,
        'conditions' => is_int($item['conditions_status']) ? $item['conditions_status'] : null,
        'frequentation' => isset($item['frequentation']) ? $item['frequentation'] : null,
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])))
    )
));
