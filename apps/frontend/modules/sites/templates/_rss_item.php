<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['SiteI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang?module=sites&id=$id&lang=$lang");

$data = array();
$data[] = $item['elevation'] . __('meters');
$data[] = __('routes_quantity') . ' ' . $item['routes_quantity'];
$data[] = get_paginated_value_from_list($item['site_types'], 'app_sites_site_types');
$data[] = get_paginated_value_from_list($item['rock_types'], 'mod_sites_rock_types_list');
$data[] = get_paginated_areas($item['geoassociations']);
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
