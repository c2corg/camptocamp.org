<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['SummitI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang?module=summits&id=$id&lang=$lang");

$data = array();
$data[] = $item['elevation'] . __('meters');
$data[] = get_paginated_value($item['summit_type'], 'mod_summits_summit_types_list');
$data[] = get_paginated_areas($item['geoassociations']);
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
