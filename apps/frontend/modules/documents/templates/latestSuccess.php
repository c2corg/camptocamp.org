<?php
use_helper('General');

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
    $slug = formate_slug($doc['search_name']);
    $feedItem->setLink("@document_by_id_lang_slug?module=$module&id=$id&lang=$lang&slug=$slug");
    $feedItem->setDescription($module);
    $feed->addItem($feedItem);
}

echo $feed->asXml(ESC_RAW);
