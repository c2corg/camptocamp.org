<?php
use_helper('Field', 'Link');

$item_i18n = $item['PortalI18n'][0];
$item_id = $item_i18n['id'];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php
echo list_link($item_i18n, 'portals');
?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=portals&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
