<?php
$item_i18n = $item->getRaw('HutI18n');
$item_i18n = $item_i18n[0];

$st = sfConfig::get('mod_huts_shelter_types_list');
$a = sfConfig::get('app_activities_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => geojson_geometry($item),
    'properties' => array(
        'module' => 'huts',
        'name' => $item_i18n['name'],
        'url' =>  jsonlist_url($item_i18n, 'huts'),
        'elevation' => $item['elevation'],
        'type' => $st[$item['shelter_type']],
        'staffedCapacity' => doctrine_value($item['staffed_capacity']),
        'unstaffedCapacity' => doctrine_value($item['unstaffed_capacity']),
        'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a, 0),
        'phone' => doctrine_value($item['phone']),
        'website' => doctrine_value($item['url']),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedRoutes' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' => json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
