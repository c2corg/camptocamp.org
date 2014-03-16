<?php
$item_i18n = $item->getRaw('ArticleI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'properties' => array(
        'module' => 'articles',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'articles'),
        'type' => $item['article_type'],
        'categories' => BaseDocument::convertStringToArray($item['categories']),
        'activities' => BaseDocument::convertStringToArray($item['activities']),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
