<?php
$item_i18n = $item['UserI18n'][0];
?>
<div><?php echo get_paginated_activities($item['activities']) ?></div>
<div><?php echo link_to($item['private_data']['topo_name'], '@document_by_id_lang?module=users&id=' . $item_i18n['id']
                                                           . '&lang=' . $item_i18n['culture']),
                ' (', $item['private_data']['username'], ')' ?></div>
<div><?php echo get_paginated_value($item['category'], 'mod_users_category_list') ?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=users&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
