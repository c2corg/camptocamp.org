<?php
use_helper('Date', 'General', 'Field', 'Link');

$item_i18n = $item['XreportI18n'][0];
$activities = $item['activities'];
$date_class = $date_light ? ' class="light"' : '';
?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<td><?php echo list_link($item_i18n, 'xreports') ?></td>
<td<?php echo $date_class ?>><time datetime="<?php echo $item['date'] ?>"><?php echo format_date($item['date'], 'D') ?></time></td>
<td><?php echo get_paginated_activities($activities) ?></td>
<td><?php echo displayWithSuffix($item['elevation'], 'meters') ?></td>
<td><?php echo get_paginated_value_from_list($item['event_type'], 'mod_xreports_event_type_list') ?></td>
<td><?php echo get_paginated_value($item['severity'], 'mod_xreports_severity_list') ?></td>
<td><?php echo (isset($item['rescue']) && !empty($item['rescue'])) ? __('yes') : '' ?></td>
<td><?php echo __('nb_impacted') , ' ' , $item['nb_impacted'] , '&nbsp;/&nbsp;' , $item['nb_participants'] ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=xreports&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
