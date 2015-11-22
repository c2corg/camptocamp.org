<?php
use_helper('Field', 'Link');

$item_i18n = $item['HutI18n'][0];
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php echo list_link($item_i18n, 'huts') ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value($item['shelter_type'], 'mod_huts_shelter_types_list') ?></td>
<td><?php $staffed_capacity = $item['staffed_capacity'];
          if (is_scalar($staffed_capacity))
          {
              echo $staffed_capacity;
          }
 ?></td>
<td><?php $unstaffed_capacity = $item['unstaffed_capacity'];
          if (is_scalar($unstaffed_capacity))
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
              echo link_to('<span></span>', $url, array('class' => 'external_link',
                                                        'title' => __('hut website')));
          }
 ?></td>
<td><?php if (isset($item['linked_docs']))
              include_partial('parkings/parkings4list', array('parkings' => $item['linked_docs'])) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=huts&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
<td><?php
if ($item['shelter_type'] != 5)
{
    echo (isset($item['nb_linked_docs'])) ? $item['nb_linked_docs'] : '' ;
}
?></td>
