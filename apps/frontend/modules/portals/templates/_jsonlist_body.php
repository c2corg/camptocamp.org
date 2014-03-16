<?php
$item_i18n = $item->getRaw('PortalI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'properties' => array(
        'module' => 'portals',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'portals'),
        'activities' => BaseDocument::convertStringToArray($item['activities']),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
