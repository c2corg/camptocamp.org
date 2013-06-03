<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$route = "@document_by_id_lang?module=images&id=$id&lang=$lang";

echo make_popup_title($title, 'images', $route);

$image = image_tag(image_url($document->get('filename'), 'medium'),
array('alt' => $title));

?>
<div class="popup_desc"><?php echo $image; ?></div>
<?php
