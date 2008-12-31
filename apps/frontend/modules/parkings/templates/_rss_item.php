<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['ParkingI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=parkings&id=$id&lang=$lang&slug=" . formate_slug($i18n['search_name']));

$data = array();
$data[] = $item['elevation'] . __('meters');
$data[] = get_paginated_value($item['public_transportation_rating'], 'mod_parkings_public_transportation_ratings_list');
$data[] = get_paginated_areas($item['geoassociations']);
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
