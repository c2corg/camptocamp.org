<?php
use_helper('Field');

$item_i18n = $item['ParkingI18n'][0];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=parkings&id=' . $item_i18n['id']
                                                        . '&lang=' . $item_i18n['culture']
                                                        . '&slug=' . formate_slug($item_i18n['search_name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['public_transportation_rating'], 'app_parkings_public_transportation_ratings') ?></td>
<td><?php echo field_pt_picto_if_set($item, true) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=parkings&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
