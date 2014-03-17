<?php
$item_i18n = $item->getRaw('BookI18n');
$item_i18n = $item_i18n[0];

$bt = sfCOnfig::get('mod_books_book_types_list');
$a = sfConfig::get('app_activities_list');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'properties' => array(
        'module' => 'books',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'books'),
        'types' => BaseDocument::convertStringToArrayTranslate($item['book_types'], $bt, 0),
        'author' => doctrine_value($item->get('author')),
        'editor' => doctrine_value($item->get('editor')),
        'publicationDate' => doctrine_value($item['publication_date']),
        'activities' => BaseDocument::convertStringToArrayTranslate($item['activities'], $a, 0),
        'languages' => BaseDocument::convertStringToArray($item['langs'], 0),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
