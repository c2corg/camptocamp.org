<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$route = "@document_by_id_lang?module=users&id=$id&lang=$lang";

echo make_gp_title($title, 'users');

$description = $document->get('description');
if (!empty($description)) {
    $description = truncate_description($description, $route);
} else {  
    $description = '';
}

$image = formate_thumbnail($associated_images);

if ($description || $image):
?>
<div class="gp_desc gp_iti"><?php echo $image . $description; ?></div>
<?php
endif;

echo make_c2c_link($route);