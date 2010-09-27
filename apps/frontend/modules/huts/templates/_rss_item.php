<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['HutI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=huts&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));
$feedItem->setUniqueId(sfRouting::getInstance()->getCurrentInternalUri().'_'.$id);
$feedItem->setAuthorName($item['creator']);
$feedItem->setPubdate(strtotime($item['creation_date']));

$data = array();
$data[] = $item['elevation'] . __('meters');
$data[] = get_paginated_value($item['shelter_type'], 'mod_huts_shelter_types_list');
if (isset($item['activities']) && is_string($item['activities']))
{
    $data[] = get_paginated_activities($item['activities'], true);
}
if (isset($item['geoassociations']) && count($item['geoassociations']))
{
    $data[] = get_paginated_areas($item['geoassociations']);
}
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
