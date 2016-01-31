<?php
use_helper('Field', 'Link');

$item_i18n = $item['HutI18n'][0];
?>
<div class="right"><?php echo get_paginated_activities($item['activities']) ?></div>
<div><?php echo list_link($item_i18n, 'huts') ?></div>
<div><?php echo displayWithSuffix($item['elevation'], 'meters'), ' - ',
                get_paginated_value($item['shelter_type'], 'mod_huts_shelter_types_list') ?></div>
<div><?php
$staffed_capacity = $item['staffed_capacity'];
if (is_scalar($staffed_capacity))
{
    $staffed_capacity = __('staffed_capacity short') . __('&nbsp;:') . ' ' . $staffed_capacity;
}
else
{
    $staffed_capacity = '';
}
$unstaffed_capacity = $item['unstaffed_capacity'];
if (is_scalar($unstaffed_capacity))
{
    $unstaffed_capacity = __('unstaffed_capacity short') . __('&nbsp;:') . ' ' . $unstaffed_capacity;
}
else
{
    $unstaffed_capacity = '';
}
$phone = phone_link($item['phone']);
echo _implode(' - ', array($staffed_capacity,
                           $unstaffed_capacity,
                           $phone));
?></div><?php
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
                    link_to($item['nb_comments'], '@document_comment?module=huts&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0', ' ',
                picto_tag('picto_routes', __('nb_routes')), ' ', (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '0';?>
</div>
