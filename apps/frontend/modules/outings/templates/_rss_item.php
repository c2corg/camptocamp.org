<?php
use_helper('Field');

$feedItem = new sfGeoFeedItem();
$i18n = $item['OutingI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=outings&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));
$feedItem->setUniqueId(sfRouting::getInstance()->getCurrentInternalUri().'_'.$id);
$feedItem->setAuthorName($item['creator']);
$feedItem->setPubdate(strtotime($item['creation_date']));

$data = array();
$data[] = field_raw_date_data($item, 'date');
$data[] = get_paginated_activities($item['activities'], true);
if (isset($item['conditions_status']) && is_integer($item['conditions_status']))
{
    $data[] = get_paginated_value($item['conditions_status'], 'mod_outings_conditions_statuses_list');
}
if (isset($item['geoassociations']) && count($item['geoassociations']))
{
    $data[] = get_paginated_areas($item['geoassociations']);
}
$feedItem->setDescription(implode(' - ', $data));

// FIXME: relevant? if yes add these fields in model call
//$feedItem->setLongitude($item['lon']);
//$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
