<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['AreaI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=areas&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));
$feedItem->setUniqueId(sfRouting::getInstance()->getCurrentInternalUri().'_'.$id);
$feedItem->setAuthorName($item['creator']);
$feedItem->setPubdate(strtotime($item['creation_date']));

$data = array();
$data[] = get_paginated_value($item['area_type'], 'mod_areas_area_types_list');
$feedItem->setDescription(implode(' - ', $data));

// FIXME: relevant? if yes, add lon/lat fields in model call
//$feedItem->setLongitude($item['lon']);
//$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
