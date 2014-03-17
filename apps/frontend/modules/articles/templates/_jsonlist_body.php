<?php
$item_i18n = $item->getRaw('ArticleI18n');
$item_i18n = $item_i18n[0];

$at = sfConfig::get('mod_articles_article_types_list');
$ac = sfConfig::get('mod_articles_categories_list');
$a = sfConfig::get('app_activities_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'properties' => array(
        'module' => 'articles',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'articles'),
        'type' => $at[$item['article_type']],
        'categories' => BaseDocument::convertStringToArrayTranslate($item['categories'], $ac, 0),
        'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a, 0),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
