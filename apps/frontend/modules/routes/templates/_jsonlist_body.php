<?php
use_helper('Field');
$item_i18n = $item->getRaw('RouteI18n');
$item_i18n = $item_i18n[0];
// TODO #337 cf what is done in list_body

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'routes',
        'name' => $item_i18n['name'], // TODO add best summit name
        'url' => jsonlist_url($item_i18n, 'routes'),
        'activities' => BaseDocument::convertStringToArray($item['activities']),
        'ratings' => json_decode(field_route_ratings_data($item, false, false, false, 'json')),
        'maxElevation' => check_not_empty($item->getRaw('max_elevation')) ? $item['max_elevation'] : null,
        'heightDiffUp' => check_not_empty($item->getRaw('height_diff_up')) ? $item['height_diff_up'] : null,
        'diificultiesHeight' => check_not_empty($item->getRaw('difficulties_height')) ? $item['difficulties_height'] : null,
        'facings' =>  $item['facing'],
        'nbLinkedImages' =>  isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedOutings' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' => json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
