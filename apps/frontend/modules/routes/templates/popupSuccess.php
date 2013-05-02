<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $title_prefix . __(' :') . ' ' . $document->get('name');
$route = "@document_by_id_lang_slug?module=routes&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'routes', $route);

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
    echo javascript_tag('C2C.init_slideshow();');
}
?>
<ul class="data">
<?php
li(summarize_route($document, true, true));
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>
<?php

echo javascript_tag('C2C.init_popup();');

//echo make_c2c_link($route, false, $raw);
