<?php
$item_i18n = $item['SiteI18n'][0];
if (!c2cTools::mobileVersion()): ?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<?php endif ?>
<td><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=sites&id=' . $item_i18n['id']
                                                     . '&lang=' . $item_i18n['culture']
                                                     . '&slug=' . make_slug($item_i18n['name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo $item['routes_quantity'] ?></td>
<td><?php echo get_paginated_value_from_list($item['site_types'], 'app_sites_site_types') ?></td>
<td><?php echo get_paginated_value_from_list($item['rock_types'], 'mod_sites_rock_types_list') ?></td>
<td><?php if (isset($item['linked_docs']))
              include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs'])) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=sites&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
<td><?php echo (isset($item['nb_linked_docs'])) ?  $item['nb_linked_docs'] : '' ;?></td>
