<?php use_helper('Date') ?>
<td><?php echo link_to($item['OutingI18n'][0]['name'], '@document_by_id?module=outings&id=' . $item['OutingI18n'][0]['id']) ?></td>
<td><?php echo format_date($item['date'], 'D') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo displayWithSuffix($item['height_diff_up'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['conditions_status'], 'mod_outings_conditions_statuses_list') ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
<td><?php 
$author_info =& $item['versions'][0]['history_metadata']['user_private_data'];
echo link_to($author_info[$author_info['name_to_use']], '@document_by_id?module=users&id=' . $author_info['id']);
?></td>
