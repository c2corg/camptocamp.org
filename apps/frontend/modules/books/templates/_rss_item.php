<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['BookI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=books&id=$id&lang=$lang&slug=" . formate_slug($i18n['search_name']));

$data = array();
$data[] = $item['author'];
$data[] = $item['editor'];
$data[] = get_paginated_activities($item['activities'], true);
$data[] = get_paginated_value_from_list($item['book_types'], 'mod_books_book_types_list');
$feedItem->setDescription(implode(' - ', $data));

$feed->addItem($feedItem);
