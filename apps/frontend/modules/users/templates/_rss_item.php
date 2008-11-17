<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['UserI18n'][0];
$feedItem->setTitle($item['private_data']['topo_name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang?module=users&id=$id&lang=$lang");

$data = array();
$data[] = get_paginated_areas($item['geoassociations']);
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
