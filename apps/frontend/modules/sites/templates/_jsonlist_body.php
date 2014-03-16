<?php
$item_i18n = $item->getRaw('SiteI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'sites',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'sites'),
        'elevation' => $item['elevation'],
        'routes_quantity' => $item['routes_quantity'],
        'site_types' => BaseDocument::convertStringToArray($item['site_types']),
        'rock_types' => BaseDocument::convertStringToArray($item['rock_types']),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedOutings' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' => json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
