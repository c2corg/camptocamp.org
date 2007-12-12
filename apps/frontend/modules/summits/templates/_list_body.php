<td><?php echo link_to($item['SummitI18n'][0]['name'], '@document_by_id?module=summits&id=' . $item['SummitI18n'][0]['id']) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['summit_type'], 'mod_summits_summit_types_list') ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>

