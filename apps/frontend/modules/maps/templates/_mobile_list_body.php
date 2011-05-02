<?php
use_helper('Field', 'Link');

$item_i18n = $item['MapI18n'][0];
?>
<div><?php echo list_link($item_i18n, 'maps') ?></div>
<div><?php echo get_paginated_value($item['editor'], 'mod_maps_editors_list'), ' ',
                $item['code'], ' - ',
                get_paginated_value($item['scale'], 'mod_maps_scales_list') ?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=maps&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
