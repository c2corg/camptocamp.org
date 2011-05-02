<?php
use_helper('Field', 'Link');

$item_i18n = $item['MapI18n'][0];
?>
<td><?php echo list_link($item_i18n, 'maps') ?></td>
<td><?php echo $item['code'] ?></td>
<td><?php echo get_paginated_value($item['scale'], 'mod_maps_scales_list') ?></td>
<td><?php echo get_paginated_value($item['editor'], 'mod_maps_editors_list') ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=maps&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
