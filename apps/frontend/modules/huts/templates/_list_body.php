<td><?php echo link_to($item['HutI18n'][0]['name'], '@document_by_id_lang_slug?module=huts&id=' . $item['HutI18n'][0]['id']
                                                    . '&lang=' . $item['HutI18n'][0]['culture']
                                                    . '&slug=' . formate_slug($item['HutI18n'][0]['search_name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['shelter_type'], 'mod_huts_shelter_types_list') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=huts&id='
        . $item['HutI18n'][0]['id'] . '&lang=' . $item['HutI18n'][0]['culture'])
    : '' ;?></td>
