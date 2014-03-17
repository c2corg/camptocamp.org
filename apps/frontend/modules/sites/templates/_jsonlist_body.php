<?php
$item_i18n = $item->getRaw('SiteI18n');
$item_i18n = $item_i18n[0];

$st = sfConfig::get('app_sites_site_types');
$rt = sfConfig::get('mod_sites_rock_types_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'sites',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'sites'),
        'elevation' => $item['elevation'],
        'routes_quantity' => doctrine_value($item['routes_quantity']),
        'site_types' => BaseDocument::convertStringToArrayTranslate($item['site_types'], $st, 0),
        'rock_types' => BaseDocument::convertStringToArrayTranslate($item['rock_types'], $rt, 0),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedOutings' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' => json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
