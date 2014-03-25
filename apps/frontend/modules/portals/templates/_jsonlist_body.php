<?php
$item_i18n = $item->getRaw('PortalI18n');
$item_i18n = $item_i18n[0];

$a = sfConfig::get('app_activities_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'id' => $item['id'],
    'properties' => array(
        'module' => 'portals',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'portals'),
        'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a, 0),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
