<?php
use_helper('General');

if (empty($model_i18n))
{
    $model_i18n = 'DocumentI18n';
}
$module_i18n = __($item['module']);
?>
<td><?php echo link_to($item[$model_i18n][0]['name'],
                       '@document_by_id_lang_slug?module=' . $item['module'] . '&id=' . $item['id']
                           . '&lang=' . $item[$model_i18n][0]['culture'] . '&slug=' . make_slug($item[$model_i18n][0]['name']),
                       array('hreflang' => $item_i18n['culture'])); ?></td>
<td><?php echo picto_tag('picto_' . $item['module'], $module_i18n); ?></td>
<td><?php echo (isset($item['nb_comments'])) ?
    link_to($item['nb_comments'], '@document_comment?module=' . $item['module'] . '&id='
        . $item[$model_i18n][0]['id'] . '&lang=' . $item[$model_i18n][0]['culture'])
    : '' ;?></td>
