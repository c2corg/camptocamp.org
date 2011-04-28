<?php
$item_i18n = $item['AreaI18n'][0];
?>
<div><?php echo link_to($item_i18n['name'],
                        '@document_by_id_lang_slug?module=areas&id=' . $item_i18n['id']
                            . '&lang=' . $item_i18n['culture'] . '&slug=' . make_slug($item_i18n['name']),
                        ($item_i18n['culture'] != $sf_user->getCulture() ? array('hreflang' => $item_i18n['culture']) : array())) ?></div>
<div><?php echo get_paginated_value($item['area_type'], 'mod_areas_area_types_list') ?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=areas&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
