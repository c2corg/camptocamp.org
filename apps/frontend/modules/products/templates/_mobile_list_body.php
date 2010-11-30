<?php
use_helper('Field');

$item_i18n = $item['ProductI18n'][0];
?>
<div><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=products&id=' . $item_i18n['id']
                                                    . '&lang=' . $item_i18n['culture']
                                                    . '&slug=' . make_slug($item_i18n['name'])) ?></div>
<div><?php echo displayWithSuffix($item['elevation'], 'meters'), ' - ', 
                get_paginated_value_from_list($item['product_type'], 'mod_products_types_list'); ?>
<?php if (isset($item['linked_docs']))
{
    echo '<div>', __('access'), ' ';
    include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs']));
    echo '</div>';
}
?>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=products&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
