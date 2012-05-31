<?php
use_helper('Pagination');

$module = $sf_context->getModuleName();

echo 'window.c2cwgt.insertContent("';
echo $div;
echo '",[';
$last_item = end($items);
foreach ($items as $item)
{
    $i18n = $item[c2cTools::module2model($module) . 'I18n'][0];
    $lang = $i18n['culture'];
    $id = $item['id'];
    echo '["' . htmlentities($i18n['name'], ENT_COMPAT, 'UTF-8') . '","' . url_for("@document_by_id_lang_slug?module=$module&id=$id&lang=$lang&slug=" . make_slug($i18n['name']), true) . '"]';
    if ($item != $last_item) {
        echo ',';
    }
}
echo ']';
echo ')';
