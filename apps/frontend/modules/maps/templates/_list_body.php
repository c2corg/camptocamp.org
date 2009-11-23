<td><?php echo link_to($item['MapI18n'][0]['name'], '@document_by_id_lang_slug?module=maps&id=' . $item['MapI18n'][0]['id']
                                                    . '&lang=' . $item['MapI18n'][0]['culture']
                                                    . '&slug=' . make_slug($item['MapI18n'][0]['name'])) ?></td>
<td><?php echo $item['code'] ?></td>
<td><?php echo get_paginated_value($item['scale'], 'mod_maps_scales_list') ?></td>
<td><?php echo get_paginated_value($item['editor'], 'mod_maps_editors_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=maps&id='
        . $item['MapI18n'][0]['id'] . '&lang=' . $item['MapI18n'][0]['culture'])
    : '' ;?></td>
