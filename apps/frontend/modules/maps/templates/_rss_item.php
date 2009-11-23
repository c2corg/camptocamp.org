<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['MapI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=maps&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));

$data = array();
$data[] = $item['code'];
$data[] = get_paginated_value($item['scale'], 'mod_maps_scales_list');
$data[] = get_paginated_value($item['editor'], 'mod_maps_editors_list');
$feedItem->setDescription(implode(' - ', $data));

// FIXME: relevant? if yes, add lon/lat fields in model call
//$feedItem->setLongitude($item['lon']);
//$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
