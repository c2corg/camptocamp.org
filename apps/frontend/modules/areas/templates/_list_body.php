<?php
$item_i18n = $item['AreaI18n'][0];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php echo link_to($item_i18n['name'],
                       '@document_by_id_lang_slug?module=areas&id=' . $item_i18n['id'] . '&lang=' . $item_i18n['culture'] . '&slug=' . make_slug($item_i18n['name']),
                       ($item_i18n['culture'] != $sf_user->getCulture() ? array('hreflang' => $item_i18n['culture']) : array())) ?></td>
<td><?php echo get_paginated_value($item['area_type'], 'mod_areas_area_types_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=areas&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
