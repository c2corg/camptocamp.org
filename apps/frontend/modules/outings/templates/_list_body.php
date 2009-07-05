<?php use_helper('Date', 'General') ?>
<td><?php
if(strlen($item['geom_wkt']))
{
    $has_gps_track = picto_tag('action_gps', __('has GPS track'));
}
else
{
    $has_gps_track = '';
}
echo link_to($item['OutingI18n'][0]['name'], '@document_by_id_lang_slug?module=outings&id=' . $item['OutingI18n'][0]['id']
                                                        . '&lang=' . $item['OutingI18n'][0]['culture']
                                                        . '&slug=' . formate_slug($item['OutingI18n'][0]['search_name'])) . ' ' . $has_gps_track ?></td>
<td><?php echo format_date($item['date'], 'D') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo displayWithSuffix($item['max_elevation'], 'meters') ?></td>
<td><?php echo displayWithSuffix($item['height_diff_up'], 'meters') ?></td>
<td><?php echo field_route_ratings_data($item, false) // helper is included in _list_header ?></td>
<td><?php echo get_paginated_value($item['conditions_status'], 'mod_outings_conditions_statuses_list') ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=outings&id='
        . $item['OutingI18n'][0]['id'] . '&lang=' . $item['OutingI18n'][0]['culture'])
    : '' ;?></td>
<td><?php
// get the first one that created the outing (whatever the culture) and grant him as author
// smaller document version id = older one
$documents_versions_id = null;
foreach ($item['versions'] as $version)
{
    if (!$documents_versions_id || $version['documents_versions_id'] < $documents_versions_id)
    {
        $documents_versions_id = $version['documents_versions_id'];
        $author_info_name = $version['history_metadata']['user_private_data']['topo_name'];
        $author_info_id = $version['history_metadata']['user_private_data']['id'];
    }
}
echo link_to($author_info_name, '@document_by_id?module=users&id=' . $author_info_id);
?></td>
