<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=huts&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_gp_title($title, 'huts');

$description = $document->get('description');
if (!empty($description)) {
    $description = truncate_description($description, $route);
} else {   
    $description = ''; 
}

$image = formate_thumbnail($associated_images);

?>
<div class="gp_desc gp_iti"><?php
if ($image) {
    echo $image;
}
?>
<ul class="data">
<?php
li(field_data_if_set($document, 'phone'));
li(field_url_data_if_set($document, 'url'));
li(field_data_if_set($document, 'staffed_capacity'));
li(field_data_if_set($document, 'unstaffed_capacity'));
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>

<h4><?php echo __('Routes from this hut') ?></h4>

<?php
include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                              'document' => $document,
                                              'type' => 'hr', // hut - summit, reversed
                                              'strict' => true));

echo make_c2c_link($route);
