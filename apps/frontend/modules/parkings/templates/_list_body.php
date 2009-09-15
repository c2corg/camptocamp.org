<?php
use_helper('Field');

?>
<td><?php echo link_to($item['ParkingI18n'][0]['name'], '@document_by_id_lang_slug?module=parkings&id=' . $item['ParkingI18n'][0]['id']
                                                        . '&lang=' . $item['ParkingI18n'][0]['culture']
                                                        . '&slug=' . formate_slug($item['ParkingI18n'][0]['search_name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['public_transportation_rating'], 'app_parkings_public_transportation_ratings') ?></td>
<td><?php echo field_pt_picto_if_set($item, true) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=parkings&id='
        . $item['ParkingI18n'][0]['id'] . '&lang=' . $item['ParkingI18n'][0]['culture'])
    : '' ;?></td>
