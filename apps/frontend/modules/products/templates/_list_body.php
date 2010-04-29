<?php
use_helper('Field');

$item_i18n = $item['ProductI18n'][0];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=products&id=' . $item_i18n['id']
                                                    . '&lang=' . $item_i18n['culture']
                                                    . '&slug=' . make_slug($item_i18n['name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value_from_list($item['product_type'], 'mod_products_types_list') ?></td>
<td><?php $url = strval($item['url']);
          if (check_not_empty($url))
          {
              echo '<a href="' . $url . '">', 
                   image_tag(sfConfig::get('app_static_url').'/static/images/extlink.gif',
                             array('alt'=>__('product website'), 'title'=>__('product website'))),
                   '</a>';
          }
 ?></td>
<td><?php if (isset($item['linked_docs']))
              include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs'])) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=products&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
