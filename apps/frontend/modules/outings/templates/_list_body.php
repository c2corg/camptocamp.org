<?php
use_helper('Date', 'General', 'Link');

if (strlen($item['geom_wkt']))
{
    $has_gps_track = picto_tag('action_gps', __('has GPS track'));
}
else
{
    $has_gps_track = '';
}
$item_i18n = $item['OutingI18n'][0];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php
echo list_link($item_i18n, 'outings') . ' ' . $has_gps_track ?></td>
<td><?php echo format_date($item['date'], 'D') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo displayWithSuffix($item['max_elevation'], 'meters') ?></td>
<td><?php echo displayWithSuffix($item['height_diff_up'], 'meters') ?></td>
<td><?php echo (isset($item['linked_routes'])) ? field_route_ratings_data($item, false, true) : '' ?></td>
<td><?php echo get_paginated_value($item['conditions_status'], 'mod_outings_conditions_statuses_list') ?></td>
<td><?php echo field_frequentation_picto_if_set($item, true) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=outings&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
<td><?php echo link_to($item['creator'], '@document_by_id?module=users&id=' . $item['creator_id']); ?></td>
