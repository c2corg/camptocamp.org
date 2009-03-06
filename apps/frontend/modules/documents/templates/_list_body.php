<?php
if (empty($model_i18n))
{
    $model_i18n = 'DocumentI18n';
}
$module_i18n = __($item['module']);
?>
<td><?php echo link_to($item[$model_i18n][0]['name'], '@document_by_id_lang_slug?module=' . $item['module'] . '&id=' . $item['id']
                                                      . '&lang=' . $item[$model_i18n][0]['culture']
                                                      . '&slug=' . formate_slug($item[$model_i18n][0]['search_name'])); ?></td>
<td><?php echo image_tag(sfConfig::get('app_static_url') . '/static/images/modules/' . $item['module'] . '_mini.png',
                         array('alt' => $module_i18n, 'title' => $module_i18n)); ?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=' . $item['module'] . '&id='
        . $item[$model_i18n][0]['id'] . '&lang=' . $item[$model_i18n][0]['culture'])
    : '' ;?></td>
