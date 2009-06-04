<?php
use_helper('Field');

$feedItem = new sfGeoFeedItem();

$i18n = $item['RouteI18n'][0];
$summit_i18n = $item['associations'][0]['Summit'][0]['SummitI18n'][0];
$feedItem->setTitle($summit_i18n['name'] . __('&nbsp;:') . ' ' . $i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=routes&id=$id&lang=$lang&slug=" . 
                   formate_slug($summit_i18n['search_name'] . '-' . $i18n['search_name']));

$data = array();
$data[] = get_paginated_activities($item['activities'], true);
$data[] = get_paginated_value($item['facing'], 'app_routes_facings');
$data[] = $item['height_diff_up'] . __('meters');
$data[] = field_route_ratings_data($item, false);
$data[] = get_paginated_areas($item['geoassociations']);
$feedItem->setDescription(implode(' - ', $data));

// FIXME: relevant? if yes, add lon/lat fields in model call
//$feedItem->setLongitude($item['lon']);
//$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
