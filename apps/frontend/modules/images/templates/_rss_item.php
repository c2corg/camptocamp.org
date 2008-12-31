<?php
use_helper('Link', 'MyImage');

$feedItem = new sfGeoFeedItem();

$i18n = $item['ImageI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=images&id=$id&lang=$lang&slug=" . formate_slug($i18n['search_name']));

$data = array();
$data[] = absolute_link(image_url($item['filename'], 'small', true), true);
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
