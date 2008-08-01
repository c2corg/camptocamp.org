<?php 
use_helper('Geoportail');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name');
$elevation = $document->get('elevation');
if (!empty($elevation)) {
    $title .= " - $elevation  m";
}
$route = "@document_by_id_lang?module=sites&id=$id&lang=$lang";

echo make_gp_title($title, 'sites');
$image = formate_thumbnail($associated_images);
if ($image):
?>
<p><?php echo $image; ?></p>
<?php endif; ?>

<ul class="data">
<?php
li(field_data_from_list_if_set($document, 'site_types', 'app_sites_site_types', true));
li(field_data_if_set($document, 'routes_quantity'));
li(field_data_from_list_if_set($document, 'max_rating', 'mod_sites_rock_free_ratings_list'));
li(field_data_from_list_if_set($document, 'min_rating', 'mod_sites_rock_free_ratings_list'));
?>
</ul>
<?php
echo make_c2c_link($route);
