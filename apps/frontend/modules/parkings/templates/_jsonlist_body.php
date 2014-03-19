<?php
$item_i18n = $item->getRaw('ParkingI18n');
$item_i18n = $item_i18n[0];

$sc = sfConfig::get('mod_parkings_snow_clearance_ratings_list');
$ptr = sfConfig::get('app_parkings_public_transportation_ratings');
$ptt = sfConfig::get('app_parkings_public_transportation_types');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => geojson_geometry($item),
    'properties' => array(
        'module' => 'parkings',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'parkings'),
        'elevation' => $item['elevation'],
        'lowestElevation' => doctrine_value($item['lowest_elevation']),
        'snowClearance' => @$sc[doctrine_value($item['snow_clearance_rating'])],
        'publicTransportationRating' => @$ptr[doctrine_value($item['public_transportation_rating'])],
        'publicTransportationTypes' => BaseDocument::convertStringToArrayTranslate($item['public_transportation_types'], $ptt, 0),
        'nbLinkedImage' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbLinkedRoutes' => isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0,
        'linkedAreas' => json_decode(get_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])))
    )
));
