<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['ArticleI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=articles&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));

$data = array();
$data[] = get_paginated_value_from_list($item['categories'], 'mod_articles_categories_list');
$data[] = get_paginated_activities($item['activities'], true);
$data[] = get_paginated_value($item['article_type'], 'mod_articles_article_types_list');
$feedItem->setDescription(implode(' - ', $data));

$feed->addItem($feedItem);
