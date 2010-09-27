<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['ParkingI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=parkings&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));
$feedItem->setUniqueId(sfRouting::getInstance()->getCurrentInternalUri().'_'.$id);
$feedItem->setAuthorName($item['creator']);
$feedItem->setPubdate(strtotime($item['creation_date']));

$data = array();
$data[] = $item['elevation'] . __('meters');
if (isset($item['public_transportation_rating']) && is_integer($item['public_transportation_rating']))
    $data[] = get_paginated_value($item['public_transportation_rating'], 'app_parkings_public_transportation_ratings');
}
if (isset($item['geoassociations']) && count($item['geoassociations']))
{
    $data[] = get_paginated_areas($item['geoassociations']);
}
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
