<?php
$item_i18n = $item['UserI18n'][0];
if (!c2cTools::mobileVersion()): ?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<?php endif ?>
<td><?php echo link_to($item['private_data']['topo_name'], '@document_by_id_lang?module=users&id=' . $item_i18n['id']
                                                           . '&lang=' . $item_i18n['culture']) ?></td>
<td><?php echo $item['private_data']['username'] ?></td>
<td><?php echo get_paginated_value($item['category'], 'mod_users_category_list') ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=users&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
