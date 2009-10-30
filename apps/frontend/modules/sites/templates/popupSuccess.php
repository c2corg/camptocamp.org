<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$elevation = $document->get('elevation');
if (!empty($elevation)) {
    $title .= " - $elevation&nbsp;m";
}
$route = "@document_by_id_lang_slug?module=sites&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_gp_title($title, 'sites');

$description = $document->get('description');
if (!empty($description)) {
    $description = truncate_description($description, $route);
} else {   
    $description = ''; 
}

$image = formate_thumbnail($associated_images);

if ($image)
{
    insert_popup_js();
}

?>
<div class="gp_desc"><?php
if ($image) {
    echo $image;
}
?>
<ul class="data">
<?php
li(field_data_from_list_if_set($document, 'site_types', 'app_sites_site_types', true));
li(field_data_if_set($document, 'routes_quantity'));
li(field_data_from_list_if_set($document, 'max_rating', 'mod_sites_rock_free_ratings_list'));
li(field_data_from_list_if_set($document, 'min_rating', 'mod_sites_rock_free_ratings_list'));
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>
<?php

echo make_c2c_link($route);
