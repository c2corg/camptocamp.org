<?php
use_helper('Field');
$item_i18n = $item->getRaw('RouteI18n');
$item_i18n = $item_i18n[0];
$summit = $item['associations'][0]['Summit'][0]['SummitI18n'][0]['name'];

$a = sfConfig::get('app_activities_list');
$f = sfConfig::get('app_routes_facings');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => geojson_geometry($item),
    'id' => $item['id'],
    'properties' => array(
        'module' => 'routes',
        'name' => $summit . __(' :') . ' ' . $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'routes', $summit),
        'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a, 0),
        'ratings' => field_route_ratings_data($item, false, false, false, 'json'),
        'maxElevation' => doctrine_value($item['max_elevation']),
        'heightDiffUp' => doctrine_value($item['height_diff_up']),
        'difficultiesHeight' => doctrine_value($item['difficulties_height']),
        'mainFacing' => @$f[doctrine_value($item['facing'])],
        'nbLinkedImages' =>  isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedOutings' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' =>  isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']))),
        'linkedParkings' => json_decode(get_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array()))))
    )
));
