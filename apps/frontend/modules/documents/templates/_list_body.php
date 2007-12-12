<?php
if (empty($model_i18n))
{
    $model_i18n = 'DocumentI18n';
}
?>
<td><?php echo link_to($item[$model_i18n][0]['name'], '@document_by_id?module='.$item['module'].'&id=' . $item['id']) ?></td>
<td><?php echo __($item['module']) ?></td>
<td><?php echo (strlen($item['geom_wkt'])) ? __('yes') : __('no') ;?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
