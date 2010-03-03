<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$elevation = $document->get('max_elevation');
if (!empty($elevation)) {
    $title .= " - $elevation&nbsp;m";
}
$route = "@document_by_id_lang_slug?module=routes&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'routes');

$description = $document->getRaw('description');
if (!empty($description)) {
    $description = truncate_description($description, $route, 700, true);
} else {   
    $description = ''; 
}

$image = formate_thumbnail($associated_images);

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
li(summarize_route($document, true, true));
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>
<?php

echo make_c2c_link($route, false, $raw);
