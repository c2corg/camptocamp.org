<td><?php echo link_to($item['AreaI18n'][0]['name'], '@document_by_id?module=areas&id=' . $item['AreaI18n'][0]['id']) ?></td>
<td><?php echo get_paginated_value($item['area_type'], 'mod_areas_area_types_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
