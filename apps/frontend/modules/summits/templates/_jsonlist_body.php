<?php
$item_i18n = $item->getRaw('SummitI18n');
$item_i18n = $item_i18n[0];

$st = sfConfig::get('app_summits_summit_types');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => geojson_geometry($item),
    'properties' => array(
        'module' => 'summits',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'summits'),
        'elevation' => $item['elevation'],
        'type' => @$st[doctrine_value($item['summit_type'])],
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedRoutes' => isset($item['nb_linked_docs']) ?  $item['nb_linked_docs'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])))
    )
));
