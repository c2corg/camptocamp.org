<?php
use_helper('Field');

$feedItem = new sfGeoFeedItem();

$i18n = $item['RouteI18n'][0];
$summit_i18n = $item['associations'][0]['Summit'][0]['SummitI18n'][0];
$feedItem->setTitle($summit_i18n['name'] . __('&nbsp;:') . ' ' . $i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=routes&id=$id&lang=$lang&slug=" . 
                   make_slug($summit_i18n['name'] . '-' . $i18n['name']));
$feedItem->setUniqueId(sfRouting::getInstance()->getCurrentInternalUri().'_'.$id);
$feedItem->setAuthorName($item['creator']);
$feedItem->setPubdate(strtotime($item['creation_date']));

$data = array();
$²data[] = get_paginated_activities($item['activities'], true);
$data[] = get_paginated_value($item['facing'], 'app_routes_facings');
$data[] = $item['height_diff_up'] . __('meters');
$data[] = field_route_ratings_data($item, false);
if (isset($item['geoassociations']) && count($item['geoassociations']))
{
    $data[] = get_paginated_areas($item['geoassociations']);
}
$feedItem->setDescription(implode(' - ', $data));

// FIXME: relevant? if yes, add lon/lat fields in model call
//$feedItem->setLongitude($item['lon']);
//$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
