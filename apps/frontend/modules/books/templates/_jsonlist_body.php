<?php
$item_i18n = $item->getRaw('BookI18n');
$item_i18n = $item_i18n[0];

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => null,
    'properties' => array(
        'module' => 'books',
        'name' => $item_i18n['name'],
        'url' => jsonlist_url($item_i18n, 'books'),
        'types' => BaseDocument::convertStringToArray($item['book_types']),
        'author' => $item->getRaw('author'),
        'editor' => $item->getRaw('editor'),
        'publicationDate' => $item['publication_date'],
        'activities' => BaseDocument::convertStringToArray($item['activities']),
        'languages' => BaseDocument::convertStringToArray($item['langs']),
        'nbLinkedImages' => isset($item['nb_images']) ?  $item['nb_images'] : 0,
        'nbComments' => isset($item['nb_comments']) ? $item['nb_comments'] : 0
    )
));
