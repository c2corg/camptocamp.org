<?php
if (empty($model_i18n))
{
    $model_i18n = 'DocumentI18n';
}
$module_i18n = __($item['module']);
?>
<td><?php echo link_to($item[$model_i18n][0]['name'], '@document_by_id?module='.$item['module'].'&id=' . $item['id']) ?></td>
<td><?php echo image_tag('/static/images/modules/' . $item['module'] . '_mini.png', array('alt' => $module_i18n, 'title' => $module_i18n)) ?></td>
<td><?php echo (isset($item['nb_comments'])) ?  $item['nb_comments'] : '' ;?></td>
