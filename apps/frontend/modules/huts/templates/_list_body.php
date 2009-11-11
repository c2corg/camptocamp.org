<?php
use_helper('Field');

$item_i18n = $item['HutI18n'][0];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=huts&id=' . $item_i18n['id']
                                                    . '&lang=' . $item_i18n['culture']
                                                    . '&slug=' . formate_slug($item_i18n['search_name'])) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['shelter_type'], 'mod_huts_shelter_types_list') ?></td>
<td><?php $staffed_capacity = $item['staffed_capacity'];
          if (is_scalar($staffed_capacity) && $staffed_capacity > 0)
          {
              echo $staffed_capacity;
          }
 ?></td>
<td><?php $unstaffed_capacity = $item['unstaffed_capacity'];
          if (is_scalar($unstaffed_capacity) && $unstaffed_capacity > 0)
          {
              echo $unstaffed_capacity;
          }
 ?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php $phone = $item['phone'];
          if (!empty($phone))
          {
              echo $phone;
          }
 ?></td>
<td><?php $url = strval($item['url']);
          if (check_not_empty($url))
          {
              echo '<a href="' . $url . '">', 
                   image_tag(sfConfig::get('app_static_url').'/static/images/extlink.gif',
                             array('alt'=>__('hut website'), 'title'=>__('hut website'))),
                   '</a>';
          }
 ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=huts&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
