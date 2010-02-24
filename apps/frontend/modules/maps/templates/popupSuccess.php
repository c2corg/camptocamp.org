<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$editor_list = sfConfig::get('app_maps_editors');
$title = $editor_list[$document->get('editor')] . ' ' . $document->get('code') . ' ' . $document->get('name');
$route = "@document_by_id_lang_slug?module=maps&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_gp_title($title, 'maps');

$description = $document->get('description');
if (!empty($description)) {
    $description = truncate_description($description, $route, 700, true);
} else {   
    $description = ''; 
}

$image = formate_thumbnail($associated_images);

if ($image)
{
    echo insert_popup_js();
}

?>
<div class="gp_desc"><?php
if ($image) {
    echo $image;
}
?>
<ul class="data">
<?php
li(field_data_from_list_if_set($document, 'scale', 'mod_maps_scales_list'));
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>
<?php

echo make_c2c_link($route);
