<?php
use_helper('Field');

$item_i18n = $item['PortalI18n'][0];
$item_id = $item_i18n['id'];
if (!c2cTools::mobileVersion()): ?>
<td><input type="checkbox" value="<?php echo $item_i18n['id'] ;?>" name="id[]"/></td>
<?php endif ?>
<td><?php
if ($item_id == sfConfig::get('app_changerdapproche_id'))
{
    echo '<a href="http://' . sfConfig::get('app_changerdapproche_host') . '/' . $item_i18n['culture'] . '">' . $item_i18n['name'] . '</a>';
}
else
{
    echo link_to($item_i18n['name'], '@document_by_id_lang_slug?module=portals&id=' . $item_id
                                                        . '&lang=' . $item_i18n['culture']
                                                        . '&slug=' . make_slug($item_i18n['name'])) ;
}
?></td>
<td><?php echo get_paginated_activities($item['activities']) ?></td>
<td><?php include_partial('documents/regions4list', array('geoassociations' => $item['geoassociations']))?></td>
<td><?php echo (isset($item['nb_images'])) ?  $item['nb_images'] : '' ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=portals&id='
        . $item_i18n['id'] . '&lang=' . $item_i18n['culture'])
    : '' ;?></td>
