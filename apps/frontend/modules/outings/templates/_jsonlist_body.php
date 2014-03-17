<?php
use_helper('Field');
$item_i18n = $item->getRaw('OutingI18n');
$item_i18n = $item_i18n[0];

$a = sfConfig::get('app_activities_list');
$c = sfConfig::get('mod_outings_conditions_statuses_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode(gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt'))),
    'properties' => array(
        'module' => 'outings',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'outings'),
        'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a),
        'creator' => $item['creator'],
        'maxElevation' => doctrine_value($item['max_elevation']),
        'heightDiffUp' => doctrine_value($item['height_diff_up']),
        'routes_rating' => isset($item['linked_routes']) ?
                           field_route_ratings_data($item, false, false, false, 'json') : null,
        'conditions' => @$c[doctrine_value($item['conditions_status'])],
        'frequentation' => @$c[doctrine_value($item['frequentation'])],
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])))
    )
));
