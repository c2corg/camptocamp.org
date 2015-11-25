<?php
use_helper('Date', 'General', 'Field', 'Link');

$item_i18n = $item['XreportI18n'][0];
$activities = $item['activities'];
$date_class = $date_light ? ' class="light"' : '';
?>
<div class="right"><?php echo get_paginated_activities($activities) ?></div>
<div><?php echo list_link($item_i18n, 'xreports') ?></div>
<?php
echo    '<div' , $date_class , '>'
      , implode(' - ', array(
            '<time datetime="' . $item['date'] . '">' . format_date($item['date'], 'D') . '</time>'
          , displayWithSuffix($item['elevation'], 'meters')
          , get_paginated_value_from_list($item['event_type'], 'mod_xreports_event_type_list')
        ))
      , '</div>'
?>
<div><?php echo __('nb_impacted') , ' ' , $item['nb_impacted'] , '&nbsp;/&nbsp;' , $item['nb_participants'] ?></div>
<div><?php
echo    __('severity short') , ' '
      , get_paginated_value($item['severity'], 'mod_xreports_severity_list')
;
if (isset($item['rescue']) && !is_null($item['rescue']) && (bool)($item['rescue']))
{
    echo ' - ' , __('rescue short');
}
?></div>
<div><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></div>
<div><?php echo picto_tag('picto_images', __('nb_linked_images')), ' ', (isset($item['nb_images'])) ?  $item['nb_images'] : '0', ' ',
                picto_tag('action_comment', __('nb_comments')), ' ', (isset($item['nb_comments'])) ?
                    link_to($item['nb_comments'], '@document_comment?module=xreports&id='
                    . $item_i18n['id'] . '&lang=' . $item_i18n['culture']) : '0'; ?></div>
