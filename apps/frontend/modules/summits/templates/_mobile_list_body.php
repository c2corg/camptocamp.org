<?php
$item_i18n = $item['SummitI18n'][0];
?>
<div><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=summits&id=' . $item_i18n['id']
                                                       . '&lang=' . $item_i18n['culture']
                                                       . '&slug=' . make_slug($item_i18n['name'])) ?></div>
<div><?php echo _implode(' - ',
                         array(displayWithSuffix($item['elevation'], 'meters'),
                               get_paginated_value($item['summit_type'], 'app_summits_summit_types'))) ?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=summits&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0', ' ',
                picto_tag('picto_routes', __('nb_routes')), ' ', (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '0';?>
</div>
