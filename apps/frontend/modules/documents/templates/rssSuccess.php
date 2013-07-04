<?php
use_helper('Pagination');

$lang = $sf_user->getCulture();
$module = $sf_context->getModuleName();

$path = $sf_request->getPathInfo();
$path = str_replace('rss', 'list', $path);
$path = $sf_request->getUriPrefix() . $path;

$feed = new sfGeoRssFeed();
$feed->setTitle('Camptocamp.org - ' . __($module . ' list'));
$feed->setLink($path);
$feed->setFeedUrl($sf_request->getPathInfo() . $sf_request->getUriPrefix());
$feed->setAuthorName('Camptocamp.org');
$feed->setLanguage($lang);

foreach ($items as $item)
{
   get_partial("$module/rss_item", array('feed' => $feed, 'item' => $item));
}
echo $feed->asXml(ESC_RAW);
