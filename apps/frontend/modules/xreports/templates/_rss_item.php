<?php
$feedItem = new sfGeoFeedItem();

$i18n = $item['XreportI18n'][0];
$feedItem->setTitle($i18n['name']);

$id = $item['id'];
$lang = $i18n['culture'];
$feedItem->setLink("@document_by_id_lang_slug?module=xreports&id=$id&lang=$lang&slug=" . make_slug($i18n['name']));
$feedItem->setUniqueId(sfRouting::getInstance()->getCurrentInternalUri().'_'.$id);
$feedItem->setPubdate(strtotime($item['creation_date']));

$data = array();
$data[] = displayWithSuffix($item['elevation'], 'meters');
$data[] = get_paginated_activities($activities);
$data[] = get_paginated_value_from_list($item['event_type'], 'mod_xreports_event_type_list');
$data[] = get_paginated_value($item['severity'], 'mod_xreports_severity_list');
$data[] = (isset($item['rescue'])) ? 'x' : '';
$data[] = (isset($item['nb_impacted'])) ? $item['nb_impacted'] : '0';

if (isset($item['geoassociations']) && count($item['geoassociations']))
{
    $data[] = get_paginated_areas($item['geoassociations']);
}
$feedItem->setDescription(implode(' - ', $data));

$feedItem->setLongitude($item['lon']);
$feedItem->setLatitude($item['lat']);

$feed->addItem($feedItem);
