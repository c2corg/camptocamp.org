<?php
$feed = new sfGeoRssFeed();
$feed->setTitle('Camptocamp.org ');
$feed->setLink('http://www.camptocamp.org');
$feed->setAuthorName('Camptocamp.org');
//$feed->setLanguage($lang);

foreach ($documents as $doc)
{
    $feedItem = new sfGeoFeedItem();
    $feedItem->setTitle($doc['name']);

    $id = $doc['id'];
    $module = $doc['module'];
    $lang = $doc['culture'];
    $feedItem->setLink("@document_by_id_lang?module=$module&id=$id&lang=$lang");
    $feedItem->setDescription($module);
    $feed->addItem($feedItem);
}

echo $feed->asXml(ESC_RAW);
