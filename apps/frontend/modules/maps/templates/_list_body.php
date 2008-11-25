<td><?php echo link_to($item['MapI18n'][0]['name'], '@document_by_id_lang?module=maps&id=' . $item['MapI18n'][0]['id']
                                                    . '&lang=' . $item['MapI18n'][0]['culture']) ?></td>
<td><?php echo $item['code'] ?></td>
<td><?php echo get_paginated_value($item['scale'], 'mod_maps_scales_list') ?></td>
<td><?php echo get_paginated_value($item['editor'], 'mod_maps_editors_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
