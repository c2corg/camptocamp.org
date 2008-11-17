<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['DocumentI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$module = $item['module'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang?module=$module&id=$id&lang=$lang");

$feed->addItem($feedItem);
