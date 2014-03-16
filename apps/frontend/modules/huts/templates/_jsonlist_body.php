<?php
$item_i18n = $item->getRaw('HutI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'huts',
        'name' => $item_i18n['name'],
        'url' =>  jsonlist_url($item_i18n, 'huts'),
        'elevation' => $item['elevation'],
        'type' => $item['shelter_type'],
        'staffedCapacity' => (is_scalar($item['staffed_capacity']) && $item['staffed_capacity'] >= 0) ? $item['staffed_capacity'] : null,
        'unstaffedCapacity' => (is_scalar($item['unstaffed_capacity']) && $item['unstaffed_capacity'] >= 0) ? $item['unstaffed_capacity'] : null,
        'activities' => BaseDocument::convertStringToArray($item['activities']),
        'phone' => (check_not_empty((string) $item['phone'])) ? $item['phone'] : null,
        'website' => (check_not_empty((string) $item['url'])) ? $item['url'] : null,
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedRoutes' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' => json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
