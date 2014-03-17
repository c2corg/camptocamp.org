<?php
$item_i18n = $item->getRaw('UserI18n');
$item_i18n = $item_i18n[0];

$a = sfConfig::get('app_activities_list');
$c = sfConfig::get('mod_users_category_list');

// note that only public profiles are listed when no credential is provided
echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'properties' => array(
        'module' => 'users',
        'name' => $item_i18n['name'],
        'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a, 0),
        'category' => @$c[doctrine_value($item['category'])]
    )
));
