<?php
use_helper('Field');

$item_i18n = $item['ParkingI18n'][0];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=parkings&id=' . $item_i18n['id']
                                                        . '&lang=' . $item_i18n['culture']
                                                        . '&slug=' . make_slug($item_i18n['name'])) ?></td>
<td><?php
$snow_clearance_rating = $item['snow_clearance_rating'];
if (isset($item['lowest_elevation']) && is_scalar($item['lowest_elevation']) && $item['lowest_elevation'] != $item['elevation'] && $snow_clearance_rating != 4)
{
    echo $item['lowest_elevation'] . __('meters') . __('range separator') . $item['elevation'] . __('meters');
}
else if (isset($item['elevation']) && is_scalar($item['elevation']))
{
    echo $item['elevation'] . __('meters');
}
?></td>
<td><?php echo get_paginated_value($item['public_transportation_rating'], 'app_parkings_public_transportation_ratings') ?></td>
<td><?php echo field_pt_picto_if_set($item, true) ?></td>
<td><?php
if ($snow_clearance_rating != 4)
{
    echo get_paginated_value($item['snow_clearance_rating'], 'mod_parkings_snow_clearance_ratings_list');
}
?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=parkings&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
<td><?php echo (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '' ;?></td>
