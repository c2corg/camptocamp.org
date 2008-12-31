<td><?php echo link_to($item['ParkingI18n'][0]['name'], '@document_by_id_lang_slug?module=parkings&id=' . $item['ParkingI18n'][0]['id']
                                                        . '&lang=' . $item['ParkingI18n'][0]['culture']
                                                        . '&slug=' . formate_slug($item['ParkingI18n'][0]['search_name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['public_transportation_rating'], 'mod_parkings_public_transportation_ratings_list') ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
