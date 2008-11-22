<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['OutingI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang?module=outings&id=$id&lang=$lang");

$date = explode('-', $item['date']);
$feedItem->setPubdate(mktime(0, 0, 0, $date[1], $date[2], $date[0]));

$data = array();
$data[] = get_paginated_activities($item['activities'], true);
$data[] = get_paginated_value($item['conditions_status'], 'mod_outings_conditions_statuses_list');
$data[] = get_paginated_areas($item['geoassociations']);
// TODO: add height_diff/max elevation/ratings?
$feedItem->setDescription(implode(' - ', $data));

// FIXME: relevant? if yes add these fields in model call
//$feedItem->setLongitude($item['lon']);
//$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
