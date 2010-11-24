<?php
use_helper('Field');

$item_i18n = $item['HutI18n'][0];
?>
<div class="right"><?php echo get_paginated_activities($item['activities']) ?></div>
<div><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=huts&id=' . $item_i18n['id']
                                                    . '&lang=' . $item_i18n['culture']
                                                    . '&slug=' . make_slug($item_i18n['name'])) ?></div>
<div><?php echo displayWithSuffix($item['elevation'], 'meters'), ' - ',
                get_paginated_value($item['shelter_type'], 'mod_huts_shelter_types_list') ?></div>
<div><?php
$staffed_capacity = $item['staffed_capacity'];
if (is_scalar($staffed_capacity) && $staffed_capacity > 0)
{
    echo $staffed_capacity;
}
$unstaffed_capacity = $item['unstaffed_capacity'];
if (is_scalar($unstaffed_capacity) && $unstaffed_capacity > 0)
{
    echo $unstaffed_capacity;
}
$phone = $item['phone'];
if (!empty($phone))
{
    echo $phone;
}
if (isset($item['linked_docs']))
{
    echo '<div>', __('access'), ' ';
    include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs']));
    echo '</div>';
}
?>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=summits&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0', ' ',
                picto_tag('picto_routes', __('nb_routes')), ' ', (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '0';?>
</div>
