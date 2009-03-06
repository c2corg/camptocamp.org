<td><?php echo link_to($item['SummitI18n'][0]['name'], '@document_by_id_lang_slug?module=summits&id=' . $item['SummitI18n'][0]['id']
                                                       . '&lang=' . $item['SummitI18n'][0]['culture']
                                                       . '&slug=' . formate_slug($item['SummitI18n'][0]['search_name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['summit_type'], 'mod_summits_summit_types_list') ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=summits&id='
        . $item['SummitI18n'][0]['id'] . '&lang=' . $item['SummitI18n'][0]['culture'])
    : '' ;?></td>
