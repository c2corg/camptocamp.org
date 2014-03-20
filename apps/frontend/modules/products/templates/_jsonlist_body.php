<?php
$item_i18n = $item->getRaw('ProductI18n');
$item_i18n = $item_i18n[0];

$pt = sfConfig::get('mod_products_types_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => geojson_geometry($item),
    'id' => $item['id'],
    'properties' => array(
        'module' => 'products',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'products'),
        'elevation' => doctrine_value($item['elevation']),
        'productTypes' => BaseDocument::convertStringToArrayTranslate($item['product_type'], $pt, 0),
        'website' => doctrine_value($item['url']),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' =>  json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
