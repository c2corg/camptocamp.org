<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$route = "@document_by_id_lang?module=users&id=$id&lang=$lang";

echo make_popup_title($title, 'users');

$description = $document->getRaw('description');
if (!empty($description)) {
    $description = truncate_description($description, $route, 700, true);
} else {  
    $description = '';
}

$image = formate_thumbnail($associated_images);

?>
<div class="popup_desc"><?php
if ($image)
{
    echo $image;
}
?>
<ul class="data">
<?php
li(field_activities_data_if_set($document));
li(field_data_from_list_if_set($document, 'category', 'mod_users_category_list'));
?>
</ul>
<?php

if ($description)
{
    echo $description;
}
?></div>
<?php

echo make_c2c_link($route);