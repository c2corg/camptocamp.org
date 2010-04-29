<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['PortalI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=portals&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));

$data = array();
$data[] = get_paginated_activities($item['activities'], true);
$data[] = get_paginated_areas($item['geoassociations']);
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
