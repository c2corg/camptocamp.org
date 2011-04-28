<?php
use_helper('General', 'Field');

$item_i18n = $item['ParkingI18n'][0];
?>
<div><?php echo link_to($item_i18n['name'],
                        '@document_by_id_lang_slug?module=parkings&id=' . $item_i18n['id']
                            . '&lang=' . $item_i18n['culture'] . '&slug=' . make_slug($item_i18n['name']),
                        ($item_i18n['culture'] != $sf_user->getCulture() ? array('hreflang' => $item_i18n['culture']) : array())) ?></div>
<div><?php
$snow_clearance_rating = $item['snow_clearance_rating'];
$elevation_field = '';
if (isset($item['lowest_elevation']) && is_scalar($item['lowest_elevation']) && $item['lowest_elevation'] != $item['elevation'] && $snow_clearance_rating != 4)
{
    $elevation_field = $item['lowest_elevation'] . __('meters') . __('range separator') . $item['elevation'] . __('meters');
}
else if (isset($item['elevation']) && is_scalar($item['elevation']))
{
    $elevation_field = $item['elevation'] . __('meters');
}
$snow_field = '';
if (is_int($snow_clearance_rating) && $snow_clearance_rating != 4)
{
    $snow_field = get_paginated_value($item['snow_clearance_rating'], 'mod_parkings_snow_clearance_ratings_list');
}
echo _implode(' - ', array($elevation_field,
                           field_pt_picto_if_set($item, true) . ' ' .
                               get_paginated_value($item['public_transportation_rating'], 'app_parkings_public_transportation_ratings'),
                           $snow_field));
?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=parkings&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0', ' ',
                picto_tag('picto_routes', __('nb_routes')), ' ', (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '0';?>
</div>
