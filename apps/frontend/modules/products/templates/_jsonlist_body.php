<?php
$item_i18n = $item->getRaw('ProductI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'products',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'products'),
        'elevation' => $item['elevation'],
        'productTypes' => $item['product_type'],
        'website' => check_not_empty((string) $item['url']) ? $item['url'] : null,
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' =>  json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
