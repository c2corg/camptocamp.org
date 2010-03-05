<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$route = "@document_by_id_lang_slug?module=areas&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'areas');

$description = $document->getRaw('description');
if (!empty($description)) {
    $description = truncate_description($description, $route, 700, true);
} else {   
    $description = ''; 
}

$image = make_thumbnail_slideshow($associated_images);

if (!$raw && $image)
{
    echo insert_popup_js();
}

?>
<div class="popup_desc"><?php
if ($image) {
    echo $image;
}
?>
<ul class="data">
<?php
li(field_data_from_list($document, 'area_type', 'mod_areas_area_types_list'));
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>
<?php

echo make_c2c_link($route, false, $raw);
