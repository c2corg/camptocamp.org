<td><?php echo link_to($item['SiteI18n'][0]['name'], '@document_by_id_lang?module=sites&id=' . $item['SiteI18n'][0]['id']
                                                     . '&lang=' . $item['SiteI18n'][0]['culture']) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo $item['routes_quantity'] ?></td>
<td><?php echo get_paginated_value_from_list($item['site_types'], 'app_sites_site_types') ?></td>
<td><?php echo get_paginated_value_from_list($item['rock_types'], 'mod_sites_rock_types_list') ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
