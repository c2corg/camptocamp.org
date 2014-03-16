<?php
$item_i18n = $item->getRaw('ParkingI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'parkings',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'parkings'),
        'elevation' => $item['elevation'],
        'lowestElevation' => isset($item['lowest_elevation']) && is_scalar($item['lowest_elevation']) ? $item['lowest_elevation'] : null,
        'snowClearance' => is_int($item['snow_clearance_rating']) && $item['snow_clearance_rating'] != 4 ? $item['snow_clearance_rating'] : null,
        'publicTransportationTypes' => $item->getRaw('public_transportation_types'),
        'nbLinkedImage' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedRoutes' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])))
    )
));
