<td><?php echo link_to($item['private_data']['topo_name'], '@document_by_id_lang?module=users&id=' . $item['UserI18n'][0]['id']
                                                           . '&lang=' . $item['UserI18n'][0]['culture']) ?></td>
<td><?php echo get_paginated_value($item['category'], 'mod_users_category_list') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
