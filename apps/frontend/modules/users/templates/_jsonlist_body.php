<?php
$item_i18n = $item->getRaw('UserI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'properties' => array(
        'module' => 'users',
        'name' => $item_i18n['name'],
        'activities' => BaseDocument::convertStringToArray($item['activities']),
        'category' => check_not_empty($item->getRaw('category')) ? $item['category'] : null
    )
));
